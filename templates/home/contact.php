<?php $this->startSection('styles'); ?>
<link rel="stylesheet" href="/assets/css/contact.css">
<?php $this->endSection(); ?>

<div class="contact-section">
    <h2>Зв'яжіться з нами</h2>
    <form class="contact-form">
        <div class="form-group">
            <label for="name">Ваше ім'я</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Електронна пошта</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Повідомлення</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        <button type="submit" class="submit-btn">Надіслати</button>
    </form>
</div>