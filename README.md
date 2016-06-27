# UnionPay-easy-SDK
This is a light SDK of UnionPay. It can sign data send to UnionPay's server, and verify the data from UnionPay.
## How to use
Set const string `SIGN_CERT_PATH` , `VERIFY_CERT_PATH` , `SIGN_CERT_PWD` at the begining of class.
Make sure the path is relative to `UnionPay.php`. It will use `getcwd()` to load files.  
* `SIGN_CERT_PATH` : the file which used to sign data. It is usually a file with a .pfx suffix.
* `VERIFY_CERT_PATH` : this is the file to verify signed data from UnionPay. It is a file with a .cer suffix.
* `SIGN_CERT_PWD` : the password of the certification file. In test environment, it should be `'000000'`.

For example, we set `VERIFY_CERT_PATH` to `'/assets/test/acp_test_verify_sign.cer'`. That means the file can be loaded by using `file_get_contents('./assets/test/acp_test_verify_sign.cer')`.  

Then, require `UnionPay.php` in your project. Use `UnionPay\UnionPay::sign($data);` to sign `$data`(should be an array), `UnionPay\UnionPay::verify($data)` to verify `$data`.
## Interface
* UnionPay::sign
>* parameters: 
    * array &$param
>* return: 
    * bool
>* description:  
The index `'signature'` & `'certId'` will be set, and return true if the input is correct. If sign failed, return false.

* UnionPay::verify
>* parameters:
    * array $param
>* return:
    * bool
>* description:  
Return true if verify successfully. Else return false. Make sure the param is from UnionPay.

## Example
* sign
> 
```
<?php
require_once "UnionPay.php";
$data['txnAmt'] = '100';
$data['orderId'] = 'The unique order id you create';
$data['txnTime'] = date('YmdHis', time());
$data['reqReserved'] = 'The field which you want to receive in callback';
$data['version'] = '5.0.0';
$data['encoding'] = 'UTF-8';
$data['signMethod'] = '01';
$data['txnType'] = '01';
$data['txnSubType'] = '01';
$data['bizType'] = '000201';
$data['channelType'] = '07';
$data['accessType'] = '0';
$data['merId'] = 'The member id you get from UnionPay';
$data['channelType'] = '07';
$data['frontFailUrl'] = 'http://www.YourServer.com/frontFail';
$data['frontUrl'] = "http://www.YourServer.com/front";
$data['backUrl'] = "http://www.YourServer.com/back";
UnionPay\UnionPay::sign($data);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
    </head>
    <body>
    <form action="https://101.231.204.80:5000/gateway/api/frontTransReq.do " method="post">
        <?php
        foreach ($data as $k => $i)
            echo "<input type=\"text\" name=\"$k\" value='$i'/>\n";
        ?>
        <br/>
        <input type="submit"/>
    </form>
    </body>
    </html>
```
The effect of code above is to create a form for customers to complete the payment.

* verify
> 
```
<?php
require_once "UnionPay.php";
if (UnionPay\UnionPay::verify($_POST))
//The query is from UnionPay.
else
//The query is invalid.
```

