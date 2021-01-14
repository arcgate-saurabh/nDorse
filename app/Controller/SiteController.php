<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class SiteController extends AppController{
    public function beforeFilter() {
        parent::beforeFilter();
        $this->theme = 'Website';
        $this->layout = "site";
        $this->Auth->allow("index", "about", "client", "faq", "contact", "adminandwebapp", "privacypolicy", "tnc","how_to");
    }
    
    //============Index page
    public function index(){
        $this->set('title_for_layout','Home');
        $this->set('description','nDorse - About us');
         $this->set('jsIncludes', array('register', "loginCommon"));
    }
    
    //=============home page
    public function about(){
        $this->set('title_for_layout','About');
        $this->set('description','nDorse - posiitive reinforcement and feedback app');
    }
    
    //============Clients page
    public function client(){
        $this->set('title_for_layout','Client');
         $this->set('description','nDorse - Clients and their industry');
    }
    
    //============faq page
    public function faq(){
        $this->set('title_for_layout','Faq');
    }
    
    //============Contact US
    //nDorse - Contract us and links to Apps on iOS and Google play
    public function contact(){
         $this->set('title_for_layout','Contact Us');
         $this->set('description','nDorse - Contract us and links to Apps on iOS and Google play');
    }
    
    
    //============Tandc
    public function tnc(){
         $this->set('title_for_layout','Terms and Conditions');
    }
    
    //============privacy policy
    public function privacypolicy(){
         $this->set('title_for_layout','Privacy Policy');
    }
    
    //============How to video
    public function how_to(){
         $this->set('title_for_layout','How to Videos');
    }
    
}

