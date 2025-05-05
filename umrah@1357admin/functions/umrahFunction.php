<?php

class Planner
{

    public function generatePassword($userType)
    {
        $prefix = "$userType";
        $digits = array_rand(array_flip(range(0, 9)), 3);
        $uniqueDigits = implode('', $digits);
        $specialChars = ['@', '#', '$', '%', '&', '*', '!'];
        $specialChar = $specialChars[array_rand($specialChars)];
        $finalDigit = rand(0, 9);

        $password = $prefix . $uniqueDigits . $specialChar . $finalDigit;
        return $password;
    }

    function generateUserCode($ucode)
    {
        $conn = new dbClass;


        $query = "SELECT user_code FROM users 
                  WHERE user_code LIKE '$ucode%'
                  ORDER BY user_code DESC 
                  LIMIT 1";

        $result = $conn->getData($query);

        if ($result && isset($result['user_code'])) {
            $lastCode = $result['user_code'];
            $number = (int) filter_var($lastCode, FILTER_SANITIZE_NUMBER_INT);
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        $userCode = $ucode . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $userCode;
    }
}


class Customer
{
    private $ID;
    private $PassportFirst;
    private $PassportSecond;
    private $PassportPhoto;
    private $VisaStamp;
    private $FullName;
    private $PhoneNumber;
    private $EmailAddress;
    private $Password;
    private $DOB;
    private $status;
    private $Gender;
    private $Marital;
    private $Country;
    private $State;
    private $District;
    private $Address;
    private $submissionType;
    private $conndb;

    public function saveCustomerData($PassportFirst, $PassportSecond, $PassportPhoto, $VisaStamp, $FullName, $PhoneNumber, $EmailAddress, $DOB, $Gender, $Marital, $Country,  $State, $District, $Address, $userPass, $submissionType, $status, $ID)
    {
        // Set all private properties
        $this->ID = $ID;
        $this->PassportFirst = $PassportFirst;
        $this->PassportSecond = $PassportSecond;
        $this->PassportPhoto = $PassportPhoto;
        $this->VisaStamp = $VisaStamp;
        $this->FullName = $FullName;
        $this->PhoneNumber = $PhoneNumber;
        $this->EmailAddress = $EmailAddress;
        $this->DOB = $DOB;
        $this->Gender = $Gender;
        $this->Marital = $Marital;
        $this->Country = $Country;
        $this->State = $State;
        $this->status = $status;
        $this->District = $District;
        $this->Address = $Address;
        $this->submissionType = $submissionType;

        // DB connection
        $conn = new dbClass;

        if ($submissionType == 'Submit'):
            $plannerObj = new Planner();
            $Password = $plannerObj->generatePassword($userPass);

            $stmt = $conn->execute("INSERT INTO `customers`(
                `customer_name`,
                `customer_number`,
                `customer_email`,
                `customer_password`,
                `customer_dob`, 
                `customer_gender`,
                `customer_marital_status`,
                `customer_nationality`,
                `customer_state`,
                `customer_district`,
                `customer_local_address`,
                `customer_photo`,
                `passport_photo_first_page`,
                `passport_photo_second_page`,
                `visa_stamp_page`,
                 `is_active`
            ) VALUES (
                '$FullName',
                '$PhoneNumber',
                '$EmailAddress',
                '$Password',
                '$DOB',                
                '$Gender',
                '$Marital',
                '$Country',
                '$State',
                '$District',
                '$Address',
                '$PassportPhoto',
                '$PassportFirst',
                '$PassportSecond',
                '$VisaStamp',
                '$status'
            )");

            if ($stmt) {
                return $Password;
            }
            return false;

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `customers` SET 
                `customer_name` = '$FullName', 
                `customer_number` = '$PhoneNumber', 
                `customer_email` = '$EmailAddress', 
                `customer_dob` = '$DOB',            
                `customer_gender` = '$Gender', 
                `customer_marital_status` = '$Marital', 
                `customer_nationality` = '$Country', 
                `customer_state` = '$State', 
                `customer_district` = '$District', 
                `customer_local_address` = '$Address', 
                `customer_photo` = '$PassportPhoto', 
                `passport_photo_first_page` = '$PassportFirst', 
                `passport_photo_second_page` = '$PassportSecond', 
                `visa_stamp_page` = '$VisaStamp', 
                `is_active` = '$status', 
                `updated_at` = NOW() 
                WHERE `customer_id` = '$ID'");

            return $stmt;

        endif;

        return false;
    }

    function getAllCustomer()
    {
        $conn = new dbClass;
        $stmt = $conn->getAllData("SELECT * FROM `customers` ORDER BY `customer_id` DESC");
        return $stmt;
    }

    function getCustomerById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `customers` WHERE `customer_id` = '$ID'");
        return $stmt;
    }
}


class User
{
    private $ID;
    private $Name;
    private $Email;
    private $PhoneNumber;
    private $AltPhone;
    private $AccountNumber;
    private $IFSC;
    private $Branch;
    private $Country;
    private $State;
    private $status;
    private $District;
    private $Address;
    private $userPaas;
    private $userCode;
    private $userType;
    private $submissionType;
    private $conndb;

    public function saveUserData($Name, $Email, $PhoneNumber, $AltPhone, $AccountNumber, $IFSC, $Branch, $Country, $State, $status, $District, $Address, $userPaas, $userCode, $userType, $submissionType, $ID)
    {
        // Set all private properties
        $this->ID = $ID;
        $this->Name = $Name;
        $this->Email = $Email;
        $this->PhoneNumber = $PhoneNumber;
        $this->AltPhone = $AltPhone;
        $this->AccountNumber = $AccountNumber;
        $this->IFSC = $IFSC;
        $this->Branch = $Branch;
        $this->Country = $Country;
        $this->State = $State;
        $this->status = $status;
        $this->District = $District;
        $this->Address = $Address;
        $this->userType = $userType;
        $this->userCode = $userCode;
        $this->userPaas = $userPaas;
        $this->submissionType = $submissionType;

        // DB connection
        $conn = new dbClass;

        if ($submissionType == 'Submit'):

            $plannerObj = new Planner();
            $Password = $plannerObj->generatePassword($userPaas);
            $userCode = $plannerObj->generateUserCode($userCode);


            $stmt = $conn->execute("INSERT INTO `users` (`user_type`, `user_code`, `user_name`, `user_email`, `user_password`, `user_mobile`, `user_alternate_number`, `user_country`,
                `user_state`, `is_active`, `user_district`, `user_address`,   `user_account_number`, `ifsc_code`, `branch`
            ) VALUES ('$userType','$userCode', '$Name', '$Email', '$Password', '$PhoneNumber', '$AltPhone',
                '$Country', '$State', '$status', '$District', '$Address',  '$AccountNumber', '$IFSC', '$Branch'
            )");

            if ($stmt) {
                return $Password;
            }
            return false;

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `users` SET 
                `user_name` = '$Name', 
                `user_email` = '$Email', 
                `user_mobile` = '$PhoneNumber', 
                `user_alternate_number` = '$AltPhone', 
                `user_country` = '$Country', 
                `user_state` = '$State', 
                `is_active` = '$status', 
                `user_district` = '$District', 
                `user_address` = '$Address', 
                `user_account_number` = '$AccountNumber', 
                `ifsc_code` = '$IFSC', 
                `branch` = '$Branch', 
                `updated_at` = now() 
                WHERE `user_id` = '$ID'");

            return $stmt;

        endif;

        return false;
    }

    function getAllUser($userType)
    {
        $conn = new dbClass;
        $this->userType = $userType;
        $stmt = $conn->getAllData("SELECT * FROM `users` WHERE 	user_type = '$userType' ORDER BY `user_id` DESC");
        return $stmt;
    }

    function getUserById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `users` WHERE `user_id` = '$ID'");
        return $stmt;
    }
}


class Banner
{
    private $ID;
    private $BannerImage;
    private $status;
    private $conndb;


    public function saveBannerData($BannerImage, $submissionType, $status, $ID)
    {

        $this->BannerImage = $BannerImage;
        $this->status = $status;
        $this->ID = $ID;

        $conn = new dbClass;

        if ($submissionType == 'Submit'):
            $stmt = $conn->execute("INSERT INTO `banner` (`banner`, `status`) VALUES ('$BannerImage', '$status')");

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `banner` SET `banner` = '$BannerImage', `updated_at` = NOW() WHERE `banner_id` = '$ID'");

        endif;

        return $stmt;
    }


    public function getAllBanners()
    {
        $conn = new dbClass;
        $stmt = $conn->getAllData("SELECT * FROM `banner` ORDER BY `banner_id` DESC");
        return $stmt;
    }

    public function getBannerById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `banner` WHERE `banner_id` = '$ID'");
        return $stmt;
    }
}


class Brand
{
    private $ID;
    private $BrandImage;
    private $status;
    private $type;
    private $conndb;

    public function saveBrandData($BrandImage, $submissionType, $status, $type, $ID)
    {
        $this->BrandImage = $BrandImage;
        $this->status = $status;
        $this->type = $type;
        $this->ID = $ID;

        $conn = new dbClass;

        if ($submissionType == 'Submit'):
            $stmt = $conn->execute("INSERT INTO `brands` (`images`, `status`, `type`) VALUES ('$BrandImage', '$status', '$type')");

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `brands` SET `images` = '$BrandImage', `status` = '$status', `type` = '$type', `updated_at` = NOW() WHERE `brands_id` = '$ID'");

        endif;

        return $stmt;
    }

    public function getAllBrands($type)
    {
        $conn = new dbClass;
        $this->type = $type;
        $stmt = $conn->getAllData("SELECT * FROM `brands` WHERE type = '$type' ORDER BY `brands_id` DESC");
        return $stmt;
    }




    public function getBrandById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `brands` WHERE `brands_id` = '$ID'");
        return $stmt;
    }
}


class Policy
{
    private $ID;
    private $PrivacyContent;
    private $status;
    private $type;
    private $conndb;


    public function savePolicyData($PrivacyContent, $status, $type, $ID)
    {
        $this->PrivacyContent = $PrivacyContent;
        $this->status = $status;
        $this->type = $type;
        $this->ID = $ID;

        $conn = new dbClass;

        $stmt = $conn->execute("UPDATE policy SET privacy = '$PrivacyContent', status = '$status', type = '$type', updated_at = NOW() WHERE privacy_id = '$ID'");


        return $stmt;
    }

    public function getAllPolicies($type)
    {
        $conn = new dbClass;
        $this->type = $type;
        $stmt = $conn->getAllData("SELECT * FROM `policy` WHERE type = '$type' ORDER BY privacy_id DESC");
        return $stmt;
    }

    public function getPolicyById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `policy` WHERE privacy_id = '$ID'");
        return $stmt;
    }
}


class Advertisement
{
    private $ID;
    private $AdContent;
    private $status;
    private $type;
    private $conndb;

    public function saveAdvertisementData($AdContent, $status, $type, $ID)
    {
        $this->AdContent = $AdContent;
        $this->status = $status;
        $this->type = $type;
        $this->ID = $ID;

        $conn = new dbClass;

        $stmt = $conn->execute("UPDATE advertisement SET images = '$AdContent', status = '$status', type = '$type', updated_at = NOW() WHERE id = '$ID'");

        return $stmt;
    }

    public function getAllAdvertisements($type)
    {
        $conn = new dbClass;
        $this->type = $type;
        $stmt = $conn->getAllData("SELECT * FROM `advertisement` WHERE type = '$type' ORDER BY id  DESC");
        return $stmt;
    }

    public function getAdvertisementById($ID)
    {
        $conn = new dbClass;
        $this->ID = $ID;
        $stmt = $conn->getData("SELECT * FROM `advertisement` WHERE id  = '$ID'");
        return $stmt;
    }
}




class Category
{
    private $cat_id;
    private $cat_img;
    private $cat_name;
    private $status;
    private $conndb;

    public function saveCategoryData($cat_name, $cat_img, $status, $submissionType, $cat_id)
    {
        $this->cat_name = $cat_name;
        $this->cat_img = $cat_img;
        $this->status = $status;
        $this->cat_id = $cat_id;

        $conn = new dbClass;

        if ($submissionType == 'Submit'):
            $stmt = $conn->execute("INSERT INTO `category` (`cat_name`, `cat_img`, `status`, `created_at`, `updated_at`) VALUES ('$cat_name', '$cat_img', '$status', NOW(), NOW())");

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `category` SET `cat_name` = '$cat_name', `cat_img` = '$cat_img', `status` = '$status', `updated_at` = NOW() WHERE `cat_id` = '$cat_id'");

        endif;

        return $stmt;
    }

    public function getAllCategories()
    {
        $conn = new dbClass;
        $stmt = $conn->getAllData("SELECT * FROM `category` ORDER BY cat_id DESC");
        return $stmt;
    }

    public function getCategoryById($cat_id)
    {
        $conn = new dbClass;
        $this->cat_id = $cat_id;
        $stmt = $conn->getData("SELECT * FROM `category` WHERE cat_id = '$cat_id'");
        return $stmt;
    }
}


class Testimonial
{
    private $id;
    private $name;
    private $feedback_msg;
    private $images;
    private $status;
    private $conndb;

    public function saveTestimonialData($name, $feedback_msg, $images, $status, $submissionType, $id)
    {
        $this->name = $name;
        $this->feedback_msg = $feedback_msg;
        $this->images = $images;
        $this->status = $status;
        $this->id = $id;

        $conn = new dbClass;

        if ($submissionType == 'Submit'):
            $stmt = $conn->execute("INSERT INTO `testimonial` (`name`, `feedback_msg`, `images`, `status`, `created_at`, `updated_at`) VALUES ('$name', '$feedback_msg', '$images', '$status', NOW(), NOW())");

        elseif ($submissionType == 'Update'):
            $stmt = $conn->execute("UPDATE `testimonial` SET `name` = '$name', `feedback_msg` = '$feedback_msg', `images` = '$images', `status` = '$status', `updated_at` = NOW() WHERE `id` = '$id'");

        endif;

        return $stmt;
    }

    public function getAllTestimonials()
    {
        $conn = new dbClass;
        $stmt = $conn->getAllData("SELECT * FROM `testimonial` ORDER BY id DESC");
        return $stmt;
    }

    public function getTestimonialById($id)
    {
        $conn = new dbClass;
        $this->id = $id;
        $stmt = $conn->getData("SELECT * FROM `testimonial` WHERE id = '$id'");
        return $stmt;
    }
}


class Heading
{
    private $id;
    private $heading;
    private $paragraph;
    private $status;
    private $type;
    private $conndb;

    public function saveContentData($heading, $paragraph, $status, $type, $submissionType, $id)
    {
        $this->heading = $heading;
        $this->paragraph = $paragraph;
        $this->status = $status;
        $this->type = $type;
        $this->id = $id;

        $conn = new dbClass;

        if ($submissionType == 'Submit') {
            $stmt = $conn->execute("INSERT INTO `all_heading` (`heading`, `paragraph`, `status`, `type`, `created_at`, `updated_at`) VALUES ('$heading', '$paragraph', '$status', '$type', NOW(), NOW())");
        } elseif ($submissionType == 'Update') {
            $stmt = $conn->execute("UPDATE `all_heading` SET `heading` = '$heading', `paragraph` = '$paragraph', `type` = '$type', `updated_at` = NOW() WHERE `id` = '$id'");
        }

        return $stmt;
    }

    public function getAllContent($type)
    {
        $conn = new dbClass;
        $this->type = $type;
        $stmt = $conn->getAllData("SELECT * FROM `all_heading` WHERE type = '$type' ORDER BY `id` DESC");
        return $stmt;
    }

    public function getContentById($id)
    {
        $conn = new dbClass;
        $this->id = $id;
        $stmt = $conn->getData("SELECT * FROM `all_heading` WHERE id = '$id'");
        return $stmt;
    }
}
