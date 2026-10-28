<?php $pageTitle = 'Data Explorer'; $extraJs = ['explorer.js']; include 'includes/header.php'; ?>
<h1>Explore StudentHub Data</h1>
<p>Select a data set loaded from external JSON files with the Fetch API, then search, filter, sort and page through the records.</p>
<section id="explorer" data-base="<?= e(url('data/')) ?>">
    <div class="controls">
        <div class="control"><label for="dataType">Choose data</label><select id="dataType"><option value="events">Events</option><option value="notices">Notices</option><option value="profiles">Student Profiles</option><option value="faqs">FAQs</option></select></div>
        <div class="control"><label for="searchInput">Search</label><input id="searchInput" type="search" placeholder="Search by keyword..."></div>
        <div class="control"><label for="categoryFilter">Filter category</label><select id="categoryFilter"><option value="all">All categories</option></select></div>
        <div class="control"><label for="sortBy">Sort by</label><select id="sortBy"><option value="default">Default order</option><option value="title-asc">Title / name (A-Z)</option><option value="title-desc">Title / name (Z-A)</option><option value="date-asc">Date / year (oldest first)</option><option value="date-desc">Date / year (newest first)</option></select></div>
        <div class="control"><label for="pageSize">Records per page</label><select id="pageSize"><option>3</option><option selected>6</option><option>9</option></select></div>
    </div>
    <p id="statusMessage" class="alert info" role="status" aria-live="polite">Loading data...</p>
    <p id="resultsSummary" class="muted"></p>
    <div id="recordList" class="grid" aria-live="polite"></div>
    <nav class="pagination" aria-label="Result pages"><button id="prevBtn" type="button">&larr; Previous</button><span id="pageInfo">Page 1</span><button id="nextBtn" type="button">Next &rarr;</button></nav>
</section>
<?php include 'includes/footer.php'; ?>
