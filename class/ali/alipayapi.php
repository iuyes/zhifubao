<?php
require '/var/www/base.php';
require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝标准双接口接口</title>
</head>
<?php
/* *
 * 功能：标准双接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */


/**************************请求参数**************************/


        $product_id = 1;
        //订单名称
        $subject = '订单名称';
        //必填
        
        //付款金额
        $price = 0.01;
        //必填

        //商品数量
        $quantity = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品

        //物流费用
        $logistics_fee = "0.00";
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "SELLER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述
        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，如：http://www.xxx.com/myorder.html
        //收货人姓名
        $receive_name = $_POST['WIDreceive_name'];
        //如：张三
        //收货人地址
        $receive_address = $_POST['WIDreceive_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        //收货人邮编
        $receive_zip = $_POST['WIDreceive_zip'];
        //如：123456
        //收货人电话号码
        $receive_phone = $_POST['WIDreceive_phone'];
        //如：0571-88158090
        //收货人手机号码
        $receive_mobile = $_POST['WIDreceive_mobile'];
        //如：13312341234


        if(!$receive_name || !$receive_address || !$receive_zip || !$receive_mobile){
            echo '请安要求填写信息';
            exit;
        }

        $address_bo = new AddressBO(null);
        $address_bo->setName($receive_name);
        $address_bo->setAddress($receive_address);
        $address_bo->setZip($receive_zip);
        $address_bo->setMobile($receive_mobile);
        $address_id = $address_bo->save();

        if(!$address_id){
            echo '当前购买用户过多，请稍后再试';
            exit;
        }


        $my_order_bo = new MyOrderBO(null);
        $my_order_bo->setAddressId($address_id);
        $my_order_bo->setProductId($product_id);
        $my_order_bo->setPrice($price);
        $my_order_bo->setLogisticsType($logistics_type);
        $my_order_bo->setLogisticsFee($logistics_fee);
        $my_order_bo->setLogisticsPayment($logistics_payment);
        $my_order_bo->setQuantity($quantity);
        $my_order_id = $my_order_bo->save();

        //商户订单号
        $out_trade_no = $my_order_bo->productOrderId($my_order_id);
        //商户网站订单系统中唯一订单号，必填

/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "trade_create_by_buyer",
		"partner" => trim($alipay_config['partner']),
		"payment_type"	=> $alipay_config['payment_type'],
		"notify_url"	=> $alipay_config['notify_url'],
		"return_url"	=> $alipay_config['return_url'],
		"seller_email"	=> $alipay_config['seller_email'],
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"price"	=> $price,
		"quantity"	=> $quantity,
		"logistics_fee"	=> $logistics_fee,
		"logistics_type"	=> $logistics_type,
		"logistics_payment"	=> $logistics_payment,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"receive_name"	=> $receive_name,
		"receive_address"	=> $receive_address,
		"receive_zip"	=> $receive_zip,
		"receive_phone"	=> $receive_phone,
		"receive_mobile"	=> $receive_mobile,
        "_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
        //"t_s_send_1" => $alipay_config['t_s_send_1'],
        //"t_b_rec_post" => $alipay_config['t_b_rec_post'],
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>
