<?php
$page_title = 'Contacts';
require_once 'includes/read_contacts.php';
$contacts = get_contacts_from_file();
include 'includes/header.php';
?>
  <div class="container">
    <h1>Contact Us</h1>
    <p>Reach out for orders, catering, or any questions. We’d love to hear from you.</p>
    <?php if (isset($contacts['error'])): ?>
      <p class="error"><?php echo htmlspecialchars($contacts['error']); ?></p>
    <?php else: ?>
      <ul class="contact-list">
        <?php foreach ($contacts as $item): ?>
          <li>
            <?php if (!empty($item['label'])): ?>
              <span class="label"><?php echo htmlspecialchars($item['label']); ?>:</span>
            <?php endif; ?>
            <span class="value"><?php echo htmlspecialchars($item['value']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php include 'includes/footer.php'; ?>
