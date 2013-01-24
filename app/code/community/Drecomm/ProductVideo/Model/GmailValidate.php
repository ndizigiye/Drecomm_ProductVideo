<?php
/**
 * Check whether the gmail account is valid
 * @package    Helper
 * @author ndizigiye
 */
class Drecomm_ProductVideo_Model_GmailValidate
extends Mage_Core_Model_Config_Data
{
    /**
    * Validates google login information after saving the configurations
    * and returns an error if information are not valid
    * @see Mage_Core_Model_Abstract::save()
    * @return Mage_Core_Model_Abstract
    */
    public function save()
    {
        $email = $this->getFieldsetDataValue('productvideo_gmail_email');
        $password = $this->getFieldsetDataValue('productvideo_gmail_password');
        $authenticationURL= 'https://www.google.com/accounts/ClientLogin';
        $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                $email,
                $password,
                $service = 'youtube',
                $client = null,
                $source = 'MySource',
                $loginToken = null,
                $loginCaptcha = null,
                $authenticationURL);
        return parent::save();
    }
}