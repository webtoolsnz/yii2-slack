# Slack LogTarget for Yii2

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webtoolsnz/yii2-slack/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webtoolsnz/yii2-slack/?branch=master)


Provides a Yii2 LogTarget implementation for slack incoming webhooks.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

`composer require webtoolsnz/yii2-slack`

## Configuration Example
You will need to [create an incoming webhook](https://my.slack.com/services/new/incoming-webhook) and configure the
`webHookUrl` setting appropriately.

~~~
...
'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'webtoolsnz\slack\SlackTarget',
                    'levels' => ['error', 'warning'],
                    'enabled' => true, // or false, if you want to turn off
                    'webHookUrl' => 'https://your.webhook.url',
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                    ],
                ],
            ],
        ],
    ],
...
~~~


## Settings
Besides the the properties inherited from [\yii\log\Target](http://www.yiiframework.com/doc-2.0/yii-log-target.html) the following settings are also supported

* `webHookUrl`: The generated URL for your incoming webhook
	* string
	* __required__
* `channel`: the channel that messages will be sent to
    * string
	* default: the setting on the webhook
* `username`: the username that messages will be sent from
	* string
	* default: Will be generated based on server name or application name (depending if web or console application)
* `icon`: the icon messages will be sent with, either :emoji: or a URL to an image
   	* string
	* default: `:warning:`
* `color`: the color of the message attachment, can be one of `good`, `warning`, `danger` or any hex color code.
	* string
	* default: `danger`
* `showFullContext`: if set to true all of the usual context variables will be included in the attachment
	* bool
	* default: `false`




