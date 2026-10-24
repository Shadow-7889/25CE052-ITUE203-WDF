// Practical 6: Fetch external JSON, dynamically render, search, filter, sort and paginate.
'use strict';
const dataSources = {
  events: { url: 'events.json', category: 'category', label: 'Events' },
  notices: { url: 'notices.json', category: 'category', label: 'Notices' },
  profiles: { url: 'profiles.json', category: 'course', label: 'Student Profiles' },
  faqs: { url: 'faqs.json', category: 'category', label: 'FAQs' }
};
const dataType = document.getElementById('dataType');
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const sortBy = document.getElementById('sortBy');
const pageSize = document.getElementById('pageSize');
const recordList = document.getElementById('recordList');
const statusMessage = document.getElementById('statusMessage');
const resultsSummary = document.getElementById('resultsSummary');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const pageInfo = document.getElementById('pageInfo');
let allRecords = [];
let currentPage = 1;
let requestNumber = 0;

async function loadRecords(type) {
  const request = ++requestNumber;
  const source = dataSources[type];
  statusMessage.className = 'status';
  statusMessage.textContent = 'Loading ' + source.label.toLowerCase() + '…';
  recordList.replaceChildren();
  resultsSummary.textContent = '';
  try {
    // Fetch API loads JSON from an external file served by Live Server.
    const response = await fetch(source.url);
    if (!response.ok) throw new Error('HTTP error: ' + response.status);
    const records = await response.json();
    if (request !== requestNumber) return;
    if (!Array.isArray(records)) throw new Error('JSON must contain an array of records.');
    allRecords = records;
    currentPage = 1;
    populateCategories(source.category);
    renderRecords();
    statusMessage.textContent = 'Loaded ' + records.length + ' ' + source.label.toLowerCase() + ' from ' + source.url + '.';
  } catch (error) {
    if (request !== requestNumber) return;
    allRecords = [];
    recordList.replaceChildren();
    statusMessage.className = 'status error';
    statusMessage.textContent = 'Could not load JSON data. Run this page with VS Code Live Server and check the file path. Details: ' + error.message;
    resultsSummary.textContent = '';
    updatePagination(0);
  }
}
function populateCategories(key) {
  const values = [...new Set(allRecords.map(item => item[key]).filter(Boolean))].sort((a,b) => String(a).localeCompare(String(b)));
  categoryFilter.replaceChildren();
  const all = document.createElement('option'); all.value = 'all'; all.textContent = 'All categories'; categoryFilter.appendChild(all);
  values.forEach(value => { const option = document.createElement('option'); option.value = value; option.textContent = value; categoryFilter.appendChild(option); });
}
function getTitle(item) { return item.title || item.name || item.question || 'Untitled record'; }
function getFilteredRecords() {
  const source = dataSources[dataType.value];
  const query = searchInput.value.trim().toLowerCase();
  const category = categoryFilter.value;
  let records = allRecords.filter(item => {
    const matchesSearch = !query || Object.values(item).some(value => String(value).toLowerCase().includes(query));
    const matchesCategory = category === 'all' || String(item[source.category] || '') === category;
    return matchesSearch && matchesCategory;
  });
  const sort = sortBy.value;
  if (sort === 'title-asc') records.sort((a,b) => getTitle(a).localeCompare(getTitle(b)));
  if (sort === 'title-desc') records.sort((a,b) => getTitle(b).localeCompare(getTitle(a)));
  if (sort === 'date-asc') records.sort((a,b) => String(a.date || a.year || '').localeCompare(String(b.date || b.year || '')));
  if (sort === 'date-desc') records.sort((a,b) => String(b.date || b.year || '').localeCompare(String(a.date || a.year || '')));
  return records;
}
function makeParagraph(label, value, className) {
  if (value === undefined || value === null || value === '') return null;
  const p = document.createElement('p');
  if (className) p.className = className;
  const strong = document.createElement('strong'); strong.textContent = label + ': ';
  p.append(strong, document.createTextNode(String(value)));
  return p;
}
function createCard(item) {
  const card = document.createElement('article'); card.className = 'record-card';
  const category = item.category || item.course;
  if (category) { const pill = document.createElement('span'); pill.className = 'category-pill'; pill.textContent = category; card.appendChild(pill); }
  const heading = document.createElement('h3'); heading.textContent = getTitle(item); card.appendChild(heading);
  let fields;
  if (dataType.value === 'events') fields = [['Date',item.date],['Venue',item.venue],['Organizer',item.organizer],['Description',item.description]];
  else if (dataType.value === 'notices') fields = [['Date',item.date],['Description',item.description]];
  else if (dataType.value === 'profiles') fields = [['Enrollment',item.enrollment],['Course',item.course],['Year',item.year],['Email',item.email]];
  else fields = [['Answer',item.answer]];
  fields.forEach(([label,value]) => { const p = makeParagraph(label,value,label === 'Date' || label === 'Enrollment' ? 'record-meta' : ''); if (p) card.appendChild(p); });
  return card;
}
function renderRecords() {
  const filtered = getFilteredRecords();
  const size = Number(pageSize.value);
  const totalPages = Math.max(1, Math.ceil(filtered.length / size));
  currentPage = Math.min(currentPage, totalPages);
  const start = (currentPage - 1) * size;
  const pageRecords = filtered.slice(start, start + size);
  recordList.replaceChildren();
  pageRecords.forEach(item => recordList.appendChild(createCard(item)));
  if (filtered.length === 0) {
    const empty = document.createElement('p'); empty.className = 'record-card'; empty.textContent = 'No records match your search/filter.'; recordList.appendChild(empty);
  }
  resultsSummary.textContent = filtered.length ? `Showing ${start + 1}–${Math.min(start + size, filtered.length)} of ${filtered.length} matching records (${allRecords.length} total).` : `Showing 0 of ${allRecords.length} records.`;
  updatePagination(totalPages);
}
function updatePagination(totalPages) {
  const size = Number(pageSize.value);
  const pages = totalPages || Math.max(1, Math.ceil(getFilteredRecords().length / size));
  pageInfo.textContent = `Page ${currentPage} of ${pages}`;
  prevBtn.disabled = currentPage <= 1;
  nextBtn.disabled = currentPage >= pages;
}
function resetAndRender() { currentPage = 1; renderRecords(); }
dataType.addEventListener('change', () => { searchInput.value = ''; sortBy.value = 'default'; loadRecords(dataType.value); });
searchInput.addEventListener('input', resetAndRender);
categoryFilter.addEventListener('change', resetAndRender);
sortBy.addEventListener('change', resetAndRender);
pageSize.addEventListener('change', resetAndRender);
prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderRecords(); } });
nextBtn.addEventListener('click', () => { const pages = Math.max(1, Math.ceil(getFilteredRecords().length / Number(pageSize.value))); if (currentPage < pages) { currentPage++; renderRecords(); } });
loadRecords(dataType.value);
