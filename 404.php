<?php
get_header(); // Include the header from your theme
?>

<!-- Main container for the 404 page -->
<div class="error-404-container">
    <div class="error-message">
        <h1>404</h1>
        <h2>عذرًا! الصفحة غير موجودة</h2>
        <p>يبدو أننا لا نستطيع العثور على ما تبحث عنه. ربما جرب البحث؟</p>
        
        <!-- Search Form -->
        <div class="search-form">
            <?php get_search_form(); ?>
        </div>

        <!-- Go Back Button -->
        <div class="back-home">
            <a href="<?php echo home_url(); ?>">العودة إلى الصفحة الرئيسية</a>
        </div>
    </div>
</div>

<?php
get_footer(); // Include the footer from your theme
?>

<!-- Add the CSS styles -->
<style>
    /* General styling */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f7f7f7;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .error-404-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
        padding: 20px;
    }

    .error-message h1 {
        font-size: 150px;
        color: #ff6347;
        margin: 0;
    }

    .error-message h2 {
        font-size: 36px;
        color: #333;
        margin: 10px 0;
    }

    .error-message p {
        font-size: 18px;
        color: #666;
        margin: 20px 0;
    }

    .search-form input[type="search"] {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 300px;
        margin: 10px 0;
    }

    .search-form input[type="submit"] {
        padding: 12px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .search-form input[type="submit"]:hover {
        background-color: #45a049;
    }

    .back-home a {
        display: inline-block;
        padding: 12px 25px;
        font-size: 18px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 20px;
    }

    .back-home a:hover {
        background-color: #45a049;
    }
</style>
