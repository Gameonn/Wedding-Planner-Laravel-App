<?php namespace App;

use Mailchimp;

class NewsletterManager {

    protected $mailchimp;
    protected $listId = 'a06f988000';        // Id of newsletter list
    protected $listId2 = 'f6a52bf916';

    /**
     * Pull the Mailchimp-instance (including API-key) from the IoC-container.
     */    
    public function __construct(Mailchimp $mailchimp) 
    {
        $this->mailchimp = $mailchimp;
    }

    /**
     * Access the mailchimp lists API
     */
    public function addEmailToList($email) 
    {
        try {
            $temp = $this->mailchimp
                ->lists
                ->subscribe(
                    $this->listId, 
                    ['email' => $email]
                );

            // dd($temp);
        } catch (\Mailchimp_List_AlreadySubscribed $e) {
            echo $e->getMessage();
        } catch (\Mailchimp_Error $e) {
            echo $e->getMessage();
        }
    }

    public function addEmailToVendorList($email) 
    {
        try {
            $temp = $this->mailchimp
                ->lists
                ->subscribe(
                    $this->listId2, 
                    ['email' => $email]
                );

            // dd($temp);
        } catch (\Mailchimp_List_AlreadySubscribed $e) {
            echo $e->getMessage();
        } catch (\Mailchimp_Error $e) {
            echo $e->getMessage();
        }
    }

    public static function test() {
        return "hu";
    }

}
