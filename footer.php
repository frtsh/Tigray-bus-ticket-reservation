<footer>
  <div class="footer-container">
    <p>&copy; <?= date('Y'); ?> Group Two. MIT Licence</p>
    <nav>
      <ul>
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="privacy.php">Privacy Policy</a></li>
      </ul>
    </nav>
  </div>

  <style>
    footer {
        background-color: #222831;
        color: #eeeeee;
        padding: 20px 0;
        text-align: center;
        font-size: 14px;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.3);
        margin-top: 40px;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    footer nav ul {
        list-style: none;
        padding: 0;
        margin: 10px 0 0 0;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    footer nav ul li a {
        color: #00adb5;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    footer nav ul li a:hover {
        color: #ffffff;
        text-decoration: underline;
    }
  </style>
</footer>
