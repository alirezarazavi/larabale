<?php

namespace AlirezaRazavi\LaraBale;

use GuzzleHttp\Client;
use AlirezaRazavi\LaraBale\Objects\Chat;
use AlirezaRazavi\LaraBale\Objects\User;
use AlirezaRazavi\LaraBale\Objects\Update;
use AlirezaRazavi\LaraBale\Objects\Message;
use AlirezaRazavi\LaraBale\FileUpload\InputFile;
use AlirezaRazavi\LaraBale\Exceptions\BaleSDKException;

/**
 * Class LaraBale
 *
 * @category Description
 * @package  Category
 * @author   Alireza Razavi <sar.razavi@gmail.com>
 * @license  https://opensource.org/licenses/MIT The MIT License
 * @link     http://alirezarazavi.com
 */
class LaraBale
{
    const BOT_TOKEN_ENV_NAME = 'BALE_BOT_TOKEN';
    private $_token = null;
    private $_baseUri;
    private $_client;
    private $_response;

    /**
     * Instantiates Bale class object.
     *
     * @param string $token The Bale Bot API Access Token.
     */
    public function __construct($token = null)
    {
        $this->_token = isset($token) ? $token : getenv(static::BOT_TOKEN_ENV_NAME);
        if (!$this->_token) {
            throw new BaleSDKException(
                'Required "token" not supplied in config and
                could not find fallback environment variable "'
                . static::BOT_TOKEN_ENV_NAME . '"'
            );
        }
        $this->_baseUri = "https://tapi.bale.ai/bot" . $this->_token . '/';
        $this->_client = new Client(array('base_uri' => $this->_baseUri));
    }

    /**
     * Set a Webhook to receive incoming updates via an outgoing webhook.
     *
     * <code>
     * $params = [
     *   'url'         => '',
     * ];
     * </code>
     *
     * @link https://devbale.ir/api#operation/setWebhook
     *
     * @param  array $params
     *
     * @var    string  $params ['url']         HTTPS url to send updates to.
     * @return array
     */
    public function setWebhook(array $params) : array
    {
        if (filter_var($params['url'], FILTER_VALIDATE_URL) === false) {
            throw new BaleSDKException('Invalid URL Provided');
        }

        if (parse_url($params['url'], PHP_URL_SCHEME) !== 'https') {
            throw new BaleSDKException('Invalid URL, should be a HTTPS url.');
        }

        return $this->_uploadFile('setWebhook', $params);
    }

    /**
     * Delete the outgoing webhook
     *
     * @return array
     */
    public function deleteWebhook(): array
    {
        return $this->_post('deleteWebhook', '');
    }

    /**
     * Use this method to receive incoming updates using long polling.
     *
     * <code>
     * $params = [
     *   'offset'  => '',
     *   'limit'   => '',
     * ];
     * </code>
     *
     * @link https://devbale.ir/api#operation/getUpdates
     *
     * @param array $params
     *
     * @var int|null $params ['offset']
     * @var int|null $params ['limit']
     *
     * @return Update[]
     */
    public function getUpdates(array $params = []): array
    {
        $response = $this->_post('getupdates', $params);
        return $response;
        $updates = $response->getDecodedBody();

        $data = [];
        if (isset($updates['result'])) {
            foreach ($updates['result'] as $update) {
                $data[] = new Update($update);
            }
        }

        return $data;
    }

    /**
     * A simple method for testing your bot's auth token.
     * Returns basic information about the bot in form of a User object.
     *
     * @link https://devbale.ir/api#operation/getMe
     *
     * @return User
     */
    public function getMe()
    {
        return $this->_get('getMe');
        // return new User($response->getDecodedBody());
    }

    /**
     * Send text messages.
     *
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'text'                     => '',
     *   'reply_to_message_id'      => '',
     *   'reply_markup'             => '',
     * ];
     * </code>
     *
     * @link https://devbale.ir/api#operation/sendMessage
     *
     * @param array    $params
     *
     * @var int|string $params ['chat_id']
     * @var string     $params ['text']
     * @var int        $params ['reply_to_message_id']
     * @var string     $params ['reply_markup']
     *
     * @return Message
     */
    public function sendMessage(array $params) : Message
    {
        $response = $this->_post('sendMessage', $params);
        return new Message($response);
    }

    /**
     * Edit text messages.
     *
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'message_id'                  => '',
     *   'text'                     => '',
     *   'reply_markup'             => '',
     * ];
     * </code>
     *
     * @link https://devbale.ir/api#operation/editMessageText
     *
     * @param array    $params
     *
     * @var int|string $params ['chat_id']
     * @var int|string $params ['message_id']
     * @var string     $params ['text']
     * @var string     $params ['reply_markup']
     *
     * @return Message
     */
    public function editMessageText(array $params) : Message
    {
        $response = $this->_post('editMessageText', $params);
        return new Message($response);
    }

    /**
     * Delete text messages.
     *
     * <code>
     * $params = [
     *   'chat_id'                  => '',
     *   'message_id'                  => '',
     * ];
     * </code>
     *
     * @link https://devbale.ir/api#operation/deleteMessage
     *
     * @param array    $params
     *
     * @var int|string $params ['chat_id']
     * @var int|string $params ['message_id']
     *
     * @return Message
     */
    public function deleteMessage(array $params) : Message
    {
        $response = $this->_post('deleteMessage', $params);
        return new Message($response);
    }

    public function getChat(array $params) : Chat
    {
        $response = $this->_post('getChat', $params);
        return new Chat($response);
    }

    public function getChatAdministrators(array $params) : Chat
    {
        $response = $this->_post('getChatAdministrators', $params);
        return new Chat($response);
    }

    public function getChatMembersCount(array $params) : Chat
    {
        $response = $this->_post('getChatMembersCount', $params);
        return new Chat($response);
    }

    public function getChatMember(array $params) : Chat
    {
        $response = $this->_post('getChatMember', $params);
        return new Chat($response);
    }

    private function _uploadFile($endpoint, array $params = [])
    {
        $i = 0;
        $multipart_params = [];
        foreach ($params as $name => $contents) {
            if (is_null($contents)) {
                continue;
            }

            if (!is_resource($contents) && $name !== 'url') {
                $validUrl = filter_var($contents, FILTER_VALIDATE_URL);
                $contents = (is_file($contents) || $validUrl) ? (new InputFile($contents))->open() : (string) $contents;
            }

            $multipart_params[$i]['name'] = $name;
            $multipart_params[$i]['contents'] = $contents;
            ++$i;
        }

        $response = $this->_post($endpoint, $multipart_params, true);

        return new Message($response->getDecodedBody());
    }

    /**
     * Undocumented function
     *
     * @param  string $endpoint   url to make request
     * @param  array  $params     to send with request
     * @param  bool   $fileUpload true or false
     * @return array  json response
     */
    private function _post(
        string $endpoint,
        array $params = [],
        bool $fileUpload = false
    ): array {
        ($fileUpload) ? $params = ['multipart' => $params]
                        : $params = ['form_params' => $params];

        $response = $this->_client->post($endpoint, $params);
        // $response = $this->_client->post($endpoint, ['query' => $params]);
        return json_decode($response->getBody(), true);
    }

    private function _get(string $endpoint, array $params = [], bool $fileUpload = false): array
    {
        // ($fileUpload) ? $params = ['multipart' => $params] : $params = ['form_params' => $params];
        $response = $this->_client->get($endpoint, $params);
        return json_decode($response->getBody()->getContents(), true);
    }
}
