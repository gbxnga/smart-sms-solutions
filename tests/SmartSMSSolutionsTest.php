<?php

/*
 * This file is part of the Smart SMS Solutions package.
 *
 * (c) Gbenga Oni <onigbenga@yahoo.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gbxnga\SmartSMSSolutions\Test;

use Gbxnga\SmartSMSSolutions\Exceptions\InvalidCredentialsException;
use Gbxnga\SmartSMSSolutions\Exceptions\InvalidParameterException;
use Gbxnga\SmartSMSSolutions\SmartSMSSolutions;
use PHPUnit\Framework\TestCase;

class SmartSMSSolutionsTest extends TestCase
{
    const SMART_SMS_SOLUTIONS_USERNAME = "<EMAIL>";

    const SMART_SMS_SOLUTIONS_PASSWORD = "<PASSWORD>";
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }


    public function getPrivateProperty(&$object, $property)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * @dataProvider providerTestValidateDateValidatesDates
     */
    public function testValidateDateValidatesDates($dateFormat, $expectedResult)
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $result = $this->invokeMethod($sms, 'validateDate', [$dateFormat]); 
        $this->assertEquals($result, $expectedResult);
    }

    public function providerTestValidateDateValidatesDates()
    {
        return [
            ["2018-02-08", false],
            ["2018-03-04 23:22:03", true],
            ["2018-03-04 23:22:03 ", true],
            ["2018-01-09 10:10", false],
            ["2018-01 09 10:10:10", false],
        ];
    }

    public function testCheckIfGetBalanceMethodReturnsBalance()
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $balance = $sms->getBalance();
        $this->assertStringMatchesFormat('%f', (string) $balance);
    }

    /**
     * @dataProvider providerTestIfConstructorThrowsInvalidCredentialsException
     */
    public function testIfConstructorThrowsInvalidCredentialsException($username, $password)
    {

        $this->expectException(InvalidCredentialsException::class);
        $sms = new SmartSMSSolutions($username, $password);
    }

    public function providerTestIfConstructorThrowsInvalidCredentialsException()
    {
        return [
            ["", ""],
            ["email@email.com", ""],
            ["", "email@email.com"],

        ];

    }

    /**
     * @dataProvider providerTestIfSendMessageMethodThrowsInvalidCredentialsException
     */
    public function testIfSendMessageMethodThrowsInvalidParameterException($sender, $recipient, $message, $schedule = null)
    {

        $this->expectException(InvalidParameterException::class);
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $sms->sendMessage($sender, $recipient, $message, $schedule = null);
    }

    public function providerTestIfSendMessageMethodThrowsInvalidCredentialsException()
    {
        return [
            ["", "", ""],
            ["email@email.com", "", ""],
            ["", "email@email.com", ""],
            ["email@email.com", "", "email@email.com"],
            ["", "email@email.com", "email@email.com"],
            ["param", "param", ""],

        ];

    }

    /**
     * @dataProvider providerTestInterpreteResponseMethodInterpretesResponse
     */
    public function testInterpreteResponseMethodInterpretesResponse($response, $expectedInterpretation)
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $interpretation = $this->invokeMethod($sms, 'interpreteResponse', [$response]);
        $this->assertEquals($expectedInterpretation, $interpretation);
    }

    public function providerTestInterpreteResponseMethodInterpretesResponse()
    { 
        return [
            ["OK","Successful"],
            ["2906", "Credit exhausted"],
            ["2916", "Sender ID not allowed"],
            ["2915", "One or more required fields are empty"],
            ["2914","Sender is empty"],
            ["2913", "Message is empty"],
            ["2912", "Recipient is empty"],
            ["2911", "Password is empty"],
            ["2910", "Username is empty"],
            ["2909", "Unable to schedule"],
            ["2908", "Invalid schedule date format"],
            ["2907", "Gateway unavailable"],
            ["2905", "Invalid username/password combination"],
            ["2904", "SMS Sending Failed"]

        ];
    }

    public function testCheckIfSetRequestOptionsMethodSetsClient()
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $property = $this->getPrivateProperty($sms, 'client');
        $this->assertEquals("GuzzleHttp\Client", get_class($property));
    }

    public function testIfSendMessageMethodSendsMessageToSingleRecipient()
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $response = $sms->sendMessage("SENDER1", "XXXXXXXXXXX","This is a message to a single recipient");
         
        $this->assertEquals("Successful", $response);
    }
    public function testIfSendMessageMethodSendsMessagesToMultipleRecipients()
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $recipients = ["XXXXXXXXXXX","XXXXXXXXXXX"];
        $response = $sms->sendMessage("SENDER2", $recipients,"This is a message to multiple recipients");
         
        $this->assertEquals("Successful", $response);
    }
    public function testIfSendMessageMethodSendsScheduledMessages()
    {
        $sms = new SmartSMSSolutions(self::SMART_SMS_SOLUTIONS_USERNAME, self::SMART_SMS_SOLUTIONS_PASSWORD);
        $recipients = ["XXXXXXXXXXX","XXXXXXXXXXX"];
        $schedule = "2018-10-22 14:09:10";
        $response = $sms->sendMessage("SENDER3", $recipients,"This is a scheduled Message for ".$schedule, $schedule);
         
        $this->assertEquals("Successful", $response);
    }

}
