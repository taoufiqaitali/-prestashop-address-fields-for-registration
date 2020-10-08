<?php

/**
 *  @author Taoufiq Ait Ali
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Regaddressfields extends Module
{
    public function __construct()
    {
        $this->name          = 'regaddressfields';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'Taoufiq Ait Ali';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('address fields for registration');
        $this->description = $this->l('Add address fields to registration form.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        $result = true;
        if (
            !parent::install()
            || !$this->registerHook('additionalCustomerFormFields')
            || !$this->registerHook('actionCustomerAccountAdd')
        ) {
            $result = false;
        }


        return $result;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        /*$formField = new FormField();
        $formField->setName('phone');
        $formField->setType('text');
        $formField->addConstraint('isPhoneNumber');
        $formField->setLabel($this->l('Phone'));
        $formField->setRequired(true);
        return array($formField);*/
    }

    public function hookActionCustomerAccountAdd($params)
    {




        try {
            //address saving
            $customerId = $params['newCustomer']->id;
            $customer = new Customer();
            $customer = $customer->getByEmail($params['newCustomer']->email);

            $address = new Address(
                null,
                $this->context->language->id
            );


            $fields = AddressFormat::getOrderedAddressFields(
                Tools::getCountry(),
                true,
                true
            );

            foreach ($fields as $field) {
                if ($field == 'id_country') continue;
                $address->$field = Tools::getValue($field);
            }

            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->id_customer = (int) $customer->id;
            $address->id_country = (int) Tools::getCountry();
            $address->id_state = 0;
            $address->alias = 'My Address';
        } catch (\Exception $th) {
            return false;
        }
        /*
 $address->id_country = (int) Tools::getCountry();
 $address->address1 = Tools::getValue('address1');
 $address->postcode = Tools::getValue('postcode');
 $address->city = Tools::getValue('city');
 $address->phone = Tools::getValue('phone');
 
 $address->firstname = $customer->firstname;
 $address->lastname = $customer->lastname;
 $address->id_customer = (int) $customer->id;
 
 $address->id_state = 0;
 $address->alias = $this->trans('My Address', [], 'Shop.Theme.Checkout');                    
 */


        return (bool)$address->save();
    }
    /*public function hookActionAdminCustomersListingFieldsModifier($params)
    {
        $params['fields']['phone'] = array(
            'title' => $this->l('Phone'),
            'align' => 'center',
        );
    }*/
}
