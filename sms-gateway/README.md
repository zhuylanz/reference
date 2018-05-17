# whmcs-sms
SMS Module for WHMCS

- Added infobip suport 
- working well with WHMCS 6.3
- Minor bug fixes

This module is folked from https://github.com/shibby/WHMCS-SmsModule 


Open Source SMS Module for WHMCS Automation.
Installation

    Upload files to your WHMCS root.
    Go to Admin Area. Enter Menu->Setup->Addon Modules and Activate Aktuel Sms
    After saving changes, give privigle to admin groups that you want at same page.
    Go to Menu->Setup->Custom Client Fields
    Add a field: name=Send Sms, type= Tick box, Show on Order Form=check. (This field will be shown at register page. If user do not check this field, SMS will not send to this user)

    Add a field: name=GSM Number, type=Text Box, Show on Order Form=check. (This field will be shown at register page. Sms will send to this value that user fills.)

    Enter Menu->Addons->Aktuel Sms
    Write WHMCS Path and Select SMS Gateway. Write your api details.

Supported SMS Gateways

    ClickAtell (Global)
    NetGsm (Turkey)
    UcuzSmsAl (Turkey)
    Mutlucell (Turkey)
    Dakik SMS (Turkey)
    msg91.com (India)
    bytehand.com (Global, Russian)
    1sms.com.tr (Türkiye)
    smsGateway.me (Global -> use your own android device to send SMS via API call)
    SendSms.ro (Romania, Global)
    Infobip (see evmaksms)

Supported Hooks

    ClientChangePassword: Send sms to user if changes account password
    TicketAdminReply: Send sms to user if admin replies user's ticket
    ClientAdd: Send sms when user register
    AfterRegistrarRegistration: Send sms to user when domain registred succesfully
    AfterRegistrarRenewal: Send sms to user when domain renewed succesfully
    AfterModuleCreate_SharedAccount: Send sms to user when hosting account created.
    AfterModuleCreate_ResellerAccount: Send sms to user when reseller account created.
    AcceptOrder: Send sms to user when order accepted manually or automatically.
    DomainRenewalNotice: Remaining to the end of {x} days prior to the domain's end time, user will be get a message.
    InvoicePaymentReminder: If there is a payment that not paid, user will be get a information message.
    InvoicePaymentReminder_FirstOverdue: Invoice payment first for seconds overdue.
    InvoicePaymentReminder_secondoverdue: Invoice payment second for seconds overdue.
    InvoicePaymentReminder_thirdoverdue: Invoice payment third for seconds overdue.
    AfterModuleSuspend: Send sms after hosting account suspended.
    AfterModuleUnsuspend: Send sms after hosting account unsuspended.
    InvoiceCreated: Send sms every invoice creation.
    AfterModuleChangePassword: After module change password.
    InvoicePaid: Whenyou have paidthe billsends a message.
