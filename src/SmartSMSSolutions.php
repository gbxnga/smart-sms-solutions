<?php

/*
 * This file is part of the Smart SMS Solutions package.
 *
 * (c) Gbenga Oni <onigbenga@yahoo.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gbxnga\SmartSMSSolutions;

use Gbxnga\SmartSMSSolutions\Exceptions\InvalidCredentialsException;
use Gbxnga\SmartSMSSolutions\Exceptions\InvalidParameterException;
use GuzzleHttp\Client;

class SmartSMSSolutions
{
    /**
     * Registered username on smartsmssolutions.com
     * @var string
     */
    protected $username;

    /**
     * Password to smartsmssolutions.com account
     * @var string
     */
    protected $password;

    /**
     * Instance of Client
     * @var Client
     */
    private $client;

    /**
     * SmartSMSSolutions API URL
     * @var string
     */
    const SMART_SMS_SOLUTIONS_API = "http://api.smartsmssolutions.com/smsapi.php";

    public function __construct(string $username, string $password)
    {
        if (empty($username) || empty($password)) {
            throw new InvalidCredentialsException("username or password cannot be empty");
        }
        $this->username = $username;
        $this->password = $password;
        $this->setRequestOptions();
    }

    /**
     * Check date againts a given format
     * @return boolean
     */

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $date = trim($date);
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Send message
     * @param string $sender
     * @param string|array $recipient
     * @param string $message
     * @param string|null $schedule
     * @return string
     * @throws InvalidParameterException
     */
    public function sendMessage(string $sender, $recipient, string $message, string $schedule = null)
    {
        if (empty($sender)) {
            throw new InvalidParameterException("sendMessage(): Parameter :sender cannot be empty");
        }

        if (empty($recipient)) {
            throw new InvalidParameterException("sendMessage(): Parameter :recipient cannot be empty");
        }

        if (empty($message)) {
            throw new InvalidParameterException("sendMessage(): Parameter :message cannot be empty");
        }

        if (is_array($recipient)) {
            $recipient = implode(",", $recipient);
        }

        $query = [
            'username' => $this->username,
            'password' => $this->password,
            'sender' => $sender,
            'recipient' => $recipient,
            'message' => $message,
        ];

        if (!is_null($schedule)) {
            if (!self::validateDate($schedule)) {
                throw new InvalidParameterException("sendMessage(): Parameter :schedule must be of format YYYY-MM-DD HH:mm:ss");
            }

            $query["schedule"] = $schedule;
        }

        $response = $this->client->request('GET', self::SMART_SMS_SOLUTIONS_API, [
            'query' => $query,
        ]);

        if ($response->getStatusCode() == "200") {
            $responseBody = (string) $response->getBody();
            $responseInterpretation = $this->interpreteResponse($responseBody);
            return $responseInterpretation ? $responseInterpretation : $responseBody;
        }

    }

    /**
     * Interpretes response returned from API,
     * Returns FALSE if response has no match
     * @param string $code the four-digit code
     * @return string|boolean
     */
    private function interpreteResponse(string $response)
    {
        switch ($response) {
            case "OK":
                return "Successful";
                break;
            case (preg_match('/OK.*/', $response) ? true : false): // if response is OK 1, OK 2 ... OK [sms charge]
                return "Successful";
                break;
            case "2906":
                return "Credit exhausted";
                break;
            case "2904":
                return "SMS Sending Failed";
                break;
            case "2905":
                return "Invalid username/password combination";
                break;
            case "2907":
                return "Gateway unavailable";
                break;
            case "2908":
                return "Invalid schedule date format";
                break;
            case "2909":
                return "Unable to schedule";
                break;
            case "2910":
                return "Username is empty";
                break;
            case "2911":
                return "Password is empty";
                break;
            case "2912":
                return "Recipient is empty";
                break;
            case "2913":
                return "Message is empty";
                break;
            case "2914":
                return "Sender is empty";
                break;
            case "2915":
                return "One or more required fields are empty";
                break;
            case "2916":
                return "Sender ID not allowed";
                break;
            default:
                return false;

        }

    }

    /**
     * Set client request
     */
    private function setRequestOptions()
    {
        $this->client = new Client();
    }

    /**
     * Get balance on account
     * @return string
     */
    public function getBalance()
    {
        $response = $this->client->request('GET', self::SMART_SMS_SOLUTIONS_API, [
            'query' => [
                'username' => $this->username,
                'password' => $this->password,
                'balance' => 'true',
            ],
        ]);
        if ($response->getStatusCode() == "200") {

            $responseBody = (string) $response->getBody();
            $responseInterpretation = $this->interpreteResponse($responseBody);
             
            return $responseInterpretation ? $responseInterpretation : $response->getBody();
        }

    }

}
