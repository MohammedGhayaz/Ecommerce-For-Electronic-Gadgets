<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Electronics Store: All You Need To Know About Us</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>

      <div class="content">
         <h3>Why Choose Us?</h3>
         <p >Welcome to our e-commerce website for electronic devices! We offer an extensive range of high-quality products at competitive prices. With a user-friendly interface and secure transactions, shopping with us is convenient and worry-free. Our fast and reliable delivery ensures your purchases reach your doorstep promptly. Plus, our dedicated customer support team is always ready to assist you. Experience the convenience, reliability, and exceptional service that sets us apart. Shop with us today and discover why we are the preferred choice for tech enthusiasts.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>

<section class="reviews">
   
   <h1 class="heading">client's reviews</h1>

   <div class="swiper reviews-slider">

   <div class="swiper-wrapper">

      <div class="swiper-slide slide">
         <img src="images/pic-1.png" alt="">
         <p>
Your e-commerce website for electronic devices is fantastic! The extensive product range, competitive prices, and user-friendly interface make shopping a breeze.  Fast delivery and dedicated customer support further enhance the overall experience. Highly recommended, Thank You!!</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>John </h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-2.png" alt="">
         <p>
         I must say, your e-commerce website for electronic devices is exceptional! The variety of high-quality products, unbeatable prices, and user-friendly interface create an outstanding shopping experience. Quick delivery and dedicated customer support add even more value. </p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Natasha</h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-3.png" alt="">
         <p>
As a customer, I had a fantastic experience exploring your e-commerce website for electronic devices. The wide selection of top-notch products, competitive prices, and intuitive interface made finding and purchasing items a breeze. The fast and reliable delivery exceeded my expectations.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Alex</h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-4.png" alt="">
         <p>
Your e-commerce website for electronic devices is truly impressive! The range of high-quality products, affordable prices, and user-friendly interface make it a go-to destination for tech enthusiasts. The efficient delivery and responsive customer support further enhance the overall satisfaction.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Sara</h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-5.png" alt="">
         <p>
Through my experience i must commend your e-commerce website for electronic devices. The seamless navigation, extensive product range, and competitive prices create an exceptional shopping experience. The prompt delivery and helpful customer support ensure a satisfying transaction.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Carlo</h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-6.png" alt="">
         <p>
I can't help but applaud your e-commerce website for electronic devices. The impressive assortment of high-quality products, attractive pricing, and user-friendly interface make it a standout choice. The swift delivery and dedicated customer support provide an unparalleled level of satisfaction.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3></h3>
      </div>

   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>









<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
        slidesPerView:1,
      },
      768: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>