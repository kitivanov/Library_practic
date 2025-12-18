const tabs = document.querySelectorAll(".nav-btn");
const tabSections = document.querySelectorAll(".tab");

const physicalTable = document.getElementById("physicalTable");
const digitalTable = document.getElementById("digitalTable");

const searchInput = document.getElementById("searchInput");
const searchBtn = document.getElementById("searchBtn");
const resetBtn = document.getElementById("resetBtn");

tabs.forEach(btn => {
  btn.addEventListener("click", () => {
    tabs.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    const tabName = btn.dataset.tab;
    tabSections.forEach(s => s.classList.remove("active"));
    document.getElementById("tab-" + tabName).classList.add("active");

    localStorage.setItem("activeTab", tabName);
  });
});

const savedTab = localStorage.getItem("activeTab");
if (savedTab) {
  tabs.forEach(b => b.classList.remove("active"));
  tabSections.forEach(s => s.classList.remove("active"));
  document.querySelector(`.nav-btn[data-tab="${savedTab}"]`).classList.add("active");
  document.getElementById("tab-" + savedTab).classList.add("active");
}

// ===== Physical Books =====
let physicalBooks = [];

async function loadPhysical() {
  const res = await fetch("/api/physical/books");
  physicalBooks = await res.json();
  renderPhysical(physicalBooks);
}

function renderPhysical(books) {
  physicalTable.innerHTML = "";
  books.forEach(book => {
    const statusText = book.status === "available" ? "Доступна" : "Выдана";
    const actionBtn = book.status === "available"
      ? `<button class="btn btn-loan" data-id="${book.inventory_number}">Выдать</button>`
      : `<span style="color:#16a34a;font-weight:600;">Выдана</span>`;
    
    physicalTable.innerHTML += `
      <tr>
        <td>${book.inventory_number}</td>
        <td>${book.title}</td>
        <td>${book.author}</td>
        <td>${statusText}</td>
        <td>${actionBtn}</td>
      </tr>
    `;
  });

  physicalTable.querySelectorAll(".btn-loan").forEach(btn => {
    btn.addEventListener("click", async () => {
      const cardNumber = prompt("Введите номер читательского билета:");
      if (!cardNumber) return;

      const res = await fetch("/api/physical/loan", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ inventory_number: btn.dataset.id, reader_card: cardNumber })
      });
      const data = await res.json();
      alert(data.message || "Книга выдана");
      loadPhysical();
    });
  });
}

// ===== Search =====
function searchPhysical() {
  const query = searchInput.value.toLowerCase().trim();
  if (!query) return renderPhysical(physicalBooks);

  const filtered = physicalBooks.filter(book =>
    book.title.toLowerCase().includes(query) ||
    book.author.toLowerCase().includes(query) ||
    book.inventory_number.toLowerCase().includes(query)
  );
  renderPhysical(filtered);
}

searchBtn.addEventListener("click", searchPhysical);
resetBtn.addEventListener("click", () => {
  searchInput.value = "";
  renderPhysical(physicalBooks);
});
searchInput.addEventListener("keydown", e => {
  if (e.key === "Enter") searchPhysical();
});

// ===== Digital Resources =====
let digitalResources = [];

async function loadDigital() {
  const res = await fetch("/api/digital/resources");
  digitalResources = await res.json();
  renderDigital(digitalResources);
}

function renderDigital(resources) {
  digitalTable.innerHTML = "";
  resources.forEach(r => {
    digitalTable.innerHTML += `
      <tr>
        <td>${r.title}</td>
        <td>${r.author}</td>
        <td>${r.format}</td>
        <td>${r.fileSize}</td>
        <td>${r.downloadCount}</td>
        <td><button class="btn btn-download" data-id="${r._id}">Скачать</button></td>
      </tr>
    `;
  });

  digitalTable.querySelectorAll(".btn-download").forEach(btn => {
    btn.addEventListener("click", async () => {
      const row = btn.closest("tr");
      const countCell = row.querySelector("td:nth-child(5)");
      const res = await fetch("/api/digital/download", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ resourceId: btn.dataset.id, userId: "USER-001" })
      });
      const data = await res.json();
      alert(`Файл доступен по ссылке: ${data.fileUrl}`);
      countCell.textContent = parseInt(countCell.textContent) + 1;
    });
  });
}

async function loadAdminUrl() {
  try {
    const res = await fetch('/api/internal/admin-url');
    if (!res.ok) throw new Error('Не удалось получить URL админки');
    const data = await res.json();
    const iframe = document.getElementById('adminIframe');
    iframe.src = data.adminUrl;
  } catch (err) {
    console.error(err);
    alert('Ошибка при загрузке админки');
  }
}

loadAdminUrl();
loadPhysical();
loadDigital();
