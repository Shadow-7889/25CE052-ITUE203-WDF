</main>
<footer class="site-footer">
    <p>&copy; 2026 StudentHub Portal &middot; CSPIT-CE &middot; ITUE203 WDF</p>
</footer>
<div class="modal" id="joinModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-content">
        <button type="button" class="modal-close" id="closeModal" aria-label="Close popup">&times;</button>
        <h2 id="modalTitle">Join StudentHub</h2>
        <p>Create an account to register for events, or log in if you already have one.</p>
        <p><a class="btn" href="<?= e(url('register.php')) ?>">Register</a> <a class="btn btn-outline" href="<?= e(url('login.php')) ?>">Login</a></p>
    </div>
</div>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
<?php foreach (($extraJs ?? []) as $js): ?>
<script src="<?= e(url('assets/js/' . $js)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
