<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Profile
{
   var $profileid;
   var $fullname;    
   var $organization;
   var $address;
   var $address2;
   var $region;
   var $postalcode;
   var $city;
   var $country;
   var $phone;
   var $image;
   var $website;
   var $userid;
   

   /* Class constructor */
   function Profile($id = null){
       if($id) {
           $sql = "Select * from users_profile where profileid='".$id."'";
           $res = DB::getInstance()->executeS($sql);
           if($res != null){
               $this->address =$res[0]['address'];
               $this->address2=$res[0]['address2'];
               $this->fullname=$res[0]['fullname'];
               $this->organization=$res[0]['organization'];
               $this->phone=$res[0]['phone'];
               $this->postalcode=$res[0]['postalcode'];
               $this->city=$res[0]['city'];
               $this->country=$res[0]['country'];
               $this->profileid = $id;
               $this->region=$res[0]['region'];
               $this->userid=$res[0]['userid'];
               $this->website=$res[0]['website'];
           }
               
       }
       
   }
   function save() {
       $db = DB::getInstance();
       
       if($this->profileid) {
           $sql = "UPDATE users_profile set ";
           $sql .= " fullname='".$this->fullname."' ,";
           $sql .= " organization='".$this->organization."' ,";
           $sql .= " address='".$this->address."' ,";
           $sql .= " address2='".$this->address2."' ,";
           $sql .= " region='".$this->region."' ,";
           $sql .= " postalcode='".$this->postalcode."' ,";
           $sql .= " city='".$this->city."' ,";
           $sql .= " country='".$this->country."' ,";
           $sql .= " phone='".$this->phone."' ,";
           $sql .= " website='".$this->website."' ,";
           $sql .= " userid='".$this->userid."' ";
           $db->execute($sql);
           return 0;
       }else {
           $sql = "Insert into users_profile ";
           $sql .= "(fullname,organization,address,address2,region,postalcode,city,country,phone,website,userid)";
           $sql .= " values  ";
           $sql .= " ('".$this->fullname."','".$this->organization."' , '".$this->address."' ,'".$this->address2."' ,'".$this->region."' ,'".$this->postalcode."' ,'".$this->city."','".$this->country."','".$this->phone."' ,'".$this->website."' ,'".$this->userid."')";
      
           $db->execute($sql);
           
           $this->profileid = $db->Insert_ID();
           
           return 0;
       }
           
       return 1;
   }
   
   function populateFromPost($post){
    
       foreach ($post as $key => $value) {
           if($key == "fullname")
                $this->fullname = $value; 
           else if($key == "organization")
                $this->organization = $value; 
           else if($key == "address")
                $this->address = $value; 
           else if($key == "address2")
                $this->address2 = $value; 
           else if($key == "region")
                $this->region = $value; 
           else if($key == "postalcode")
                $this->postalcode = $value;
           else if($key == "city")
                $this->city = $value; 
           else if($key == "country")
                $this->country = $value; 
           else if($key == "phone")
                $this->phone = $value; 
           else if($key == "website")
                $this->website = $value; 
       }
       
   }
}