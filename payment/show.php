<?php





if(isset($_GET['OK'])){ 
    $code = $_GET['code'] ?? 0; 
    $idbot = $_GET['idbot'] ?? null;
    echo "<html>
<head>
</head>
<title>پرداخت موفق!</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' />
<link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' />
<style type='text/css'>

    body
    {
        background:#f2f2f2;
    }

    .payment
  {
    border:1px solid #f2f2f2;
    height:280px;
        border-radius:20px;
        background:#fff;
  }
   .payment_header
   {
     background:rgb(35, 223, 18);
     padding:20px;
       border-radius:20px 20px 0px 0px;
     
   }
   
   .check
   {
     margin:0px auto;
     width:50px;
     height:50px;
     border-radius:100%;
     background:#fff;
     text-align:center;
   }
   
   .check i
   {
     vertical-align:middle;
     line-height:50px;
     font-size:30px;
   }

    .content 
    {
        text-align:center;
    }

    .content  h1
    {
        font-size:25px;
        padding-top:25px;
    }

    .content a
    {
        width:200px;
        height:35px;
        color:#fff;
        border-radius:30px;
        padding:5px 10px;
        background:rgb(0, 225, 255);
        transition:all ease-in-out 0.3s;
    }

    .content a:hover
    {
        text-decoration:none;
        background:rgb(255, 0, 0);
    }
   
</style>
  <body>
<div class='container'>
<div class='row'>
   <div class='col-md-6 mx-auto mt-5'>
      <div class='payment'>
         <div class='payment_header'>
            <div class='check'><i class='fa fa-check' aria-hidden='true'></i></div>
         </div>
         <div class='content'>
            <h1>پرداخت موفق</h1>
            <p>کد پیگیری : $code<br/>حساب کاربری شما در ربات با موفقیت شارژ شد</p>
            <a href='tg://resolve?domain=$idbot'>بازگشت به ربات</a>
         </div>
         
      </div>
   </div>
</div>
</div>
</body>
</html>";

}elseif(isset($_GET['NOK'])){
  $idbot = $_GET['idbot'] ?? null;
  $msg = [];
  echo "<html>
     <head>
     </head>
     <title>پرداخت ناموفق !</title>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' />
     <link rel='stylesheet' type='text/css' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' />
     <style type='text/css'>
     
         body
         {
             background:#f2f2f2;
         }
     
         .payment
       {
         border:1px solid #f2f2f2;
         height:280px;
             border-radius:20px;
             background:#fff;
       }
        .payment_header
        {
          background:rgb(248, 28, 28);
          padding:20px;
            border-radius:20px 20px 0px 0px;
          
        }
        
        .check
        {
          margin:0px auto;
          width:50px;
          height:50px;
          border-radius:100%;
          background:#fff;
          text-align:center;
        }
        
        .check i
        {
          vertical-align:middle;
          line-height:50px;
          font-size:30px;
        }
     
         .content 
         {
             text-align:center;
         }
     
         .content  h1
         {
             font-size:25px;
             padding-top:25px;
         }
     
         .content a
         {
             width:200px;
             height:35px;
             color:#fff;
             border-radius:30px;
             padding:5px 10px;
             background:rgb(0, 225, 255);
             transition:all ease-in-out 0.3s;
         }
     
         .content a:hover
         {
             text-decoration:none;
             background:rgb(255, 0, 0);
         }
        
     </style>
       <body>
     <div class='container'>
     <div class='row'>
        <div class='col-md-6 mx-auto mt-5'>
           <div class='payment'>
              <div class='payment_header'>
                 <div class='check'><i class='fa fa-check' aria-hidden='true'></i></div>
              </div>
              <div class='content'>
                 <h1>پرداخت ناموفق</h1>
                 <p>لطفا به ربات بازگشته و مجدد لینک پرداخت دریافت کنید 
     
                     توجه : درصورتی که مبلغ از حساب شما کسر شده طی 72 ساعت آینده به حساب شما عودت خواهد خورد</p>
                 <a href='tg://resolve?domain=$idbot'>بازگشت به ربات</a>
              </div>
              
           </div>
        </div>
     </div>
     </div>
     </body>
     </html>";
}
