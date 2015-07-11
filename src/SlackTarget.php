<?php

namespace webtoolsnz\slack;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class SlackTarget extends \yii\log\Target
{
    /**
     * [Optional] Channel for messages to be sent to
     * @var
     */
    public $channel;

    /**
     * [Optional] Username for the message sender, if not
     * specified one will be generated.
     * @var
     */
    public $username;

    /**
     * Message icon, can be either a image URL or emoji
     * @var string
     */
    public $icon = ':warning:';

    /**
     * Color code for message attachments
     * @var string
     */
    public $color = 'danger';

    /**
     *
     * @var
     */
    public $webHookUrl;

    /**
     * @var
     */
    public $client;

    /**
     * @var bool
     */
    public $showFullContext = false;

    /**
     * Configure the slack client.
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->username) {
            $this->username = $this->getSenderName();
        }

        if (empty($this->webHookUrl)) {
            throw new InvalidConfigException('SlackTarget::webHookUrl must be set.');
        }

        $this->client = new \Maknz\Slack\Client($this->webHookUrl, [
            'username' => $this->username,
            'channel' => $this->channel,
            'link_names' => true,
            'icon' => $this->icon
        ]);
    }

    /**
     * Sends the log message to slack.
     */
    public function export()
    {
        $message = ArrayHelper::getValue($this->messages[0], 0, 'Unknown Error');

        $this->client->attach([
            'title' => ArrayHelper::getValue($this->messages[0], 2, 'Site Error'),
            'text' => $message,
            'fallback' => $message,
            'color' => $this->color,
            'fields' => ArrayHelper::getValue($this->messages[1], 0, []),
        ])->send();
    }

    /**
     * Generates the sender name for the message
     * @return string
     */
    public function getSenderName()
    {
        $name = (Yii::$app instanceof \yii\web\Application) ? Yii::$app->getRequest()->serverName : Yii::$app->name;
        return sprintf('[%s] %s', YII_ENV, $name);
    }

    /**
     * @return array
     */
    protected function getContextMessage()
    {
        if ($this->showFullContext) {
            return $this->getFullContextMessage();
        }

        $context = (Yii::$app instanceof \yii\web\Application) ? $this->getWebContext() : $this->getConsoleContext();

        $context[] = [
            'title' => 'Time',
            'value' => date('Y-m-d H:i:s'),
            'short' => true,
        ];

        return $context;
    }

    /**
     * Returns the full set of context attachments, including all super globals
     * @return array
     */
    public function getFullContextMessage()
    {
        $context = [];
        foreach ($this->logVars as $name) {
            if (empty($GLOBALS[$name])) {
                continue;
            }

            foreach ($GLOBALS[$name] as $prop => $val) {
                $context[] = [
                    'title' => $prop,
                    'value' => $val,
                    'short' => true,
                ];
            }
        }

        return $context;
    }

    /**
     * Returns the log context for a console application
     * @return array
     */
    public function getConsoleContext()
    {
        return [
            [
                'title' => 'Params',
                'value' => implode(' ', Yii::$app->getRequest()->getParams()),
                'short' => true
            ]
        ];
    }

    /**
     * Returns the log context for a web application
     * @return array
     */
    public function getWebContext()
    {
        $request = Yii::$app->getRequest();
        $user = Yii::$app->getUser();

        return [
            [
                'title' => 'Remote IP',
                'value' => $request->getUserIP(),
                'short' => true,
            ],
            [
                'title' => 'User ID',
                'value' => $user->isGuest ? 'Guest' : $user->identity->getId(),
                'short' => true,
            ],
            [
                'title' => 'URL',
                'value' => $request->getUrl(),
                'short' => true,
            ],
            [
                'title' => 'Method',
                'value' => $request->method,
                'short' => true,
            ],
        ];
    }
}


