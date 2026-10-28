</main>
<footer class="site-footer">
    <p>&copy; 2026 StudentHub Portal &middot; <?= e(PRACTICAL) ?> &middot; CSPIT-CE &middot; ITUE203 WDF</p>
</footer>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
<?php foreach (($extraJs ?? []) as $js): ?>
<script src="<?= e(url('assets/js/' . $js)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
