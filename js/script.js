//===============Sidebar toggle===============//
// Select all option elements
const options = document.querySelectorAll('.opt');

// Function to set the active class and update images based on the current URL
function setActiveBasedOnURL() {
    const currentPath = window.location.pathname;

    options.forEach(option => {
        const img = option.querySelector('img');
        const optionPath = option.getAttribute('href'); // Get the href for the current option

        if (currentPath === optionPath) {
            option.classList.add('active');
            img.src = img.getAttribute('data-light-src'); // Use light image for active
        } else {
            option.classList.remove('active');
            img.src = img.getAttribute('data-dark-src'); // Use dark image for inactive
        }
    });
}

//===============Update Time===============//
function updateTime() {
    const timeElement = document.getElementById('wib-time');
    if (!timeElement) return; // Prevent errors if the element is missing

    let [date, time, zone] = timeElement.innerText.split(" ");
    let [day, month, year] = date.split("/");
    let [hours, minutes] = time.split(":").map(Number);

    // Increment time
    minutes++;
    if (minutes === 60) {
        minutes = 0;
        hours++;
    }
    if (hours === 24) {
        hours = 0;
        const jsDate = new Date(`${year}-${month}-${day}T00:00:00`);
        jsDate.setDate(jsDate.getDate() + 1);
        day = String(jsDate.getDate()).padStart(2, '0');
        month = String(jsDate.getMonth() + 1).padStart(2, '0');
        year = jsDate.getFullYear();
    }

    // Update display
    timeElement.innerText = `${day}/${month}/${year} ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')} WIB`;
}

//===============Sorting Table===============//
// Function to initialize sorting for all tables with the class 'sortable'
function initializeSortableTables() {
    const tables = document.querySelectorAll('.sortable'); // Select all tables with the class 'sortable'

    tables.forEach((table) => {
        const headers = table.querySelectorAll('thead th'); // Get all headers
        headers.forEach((header, columnIndex) => {
            const icon = header.querySelector('.sort-icon'); // Get the sorting icon, if exists

            header.addEventListener('click', () => sortTable(table, columnIndex, header)); // Attach click event
        });
    });
}

// Function to sort a specific table by a specific column and toggle the sorting icon
function sortTable(table, columnIndex, header) {
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.rows);
    const isAscending = table.getAttribute("data-sort-order") !== "asc"; // Toggle sort order

    // Sort rows based on the selected column
    rows.sort((a, b) => {
        const cellA = a.cells[columnIndex].innerText.toLowerCase();
        const cellB = b.cells[columnIndex].innerText.toLowerCase();

        if (cellA < cellB) return isAscending ? -1 : 1;
        if (cellA > cellB) return isAscending ? 1 : -1;
        return 0;
    });

    // Update the table with the sorted rows
    rows.forEach(row => tbody.appendChild(row));

    // Toggle the sort order for the next click
    table.setAttribute("data-sort-order", isAscending ? "asc" : "desc");

    // Update the sorting icon based on the sort order
    updateSortIcon(header, isAscending);
}

// Function to update the sorting icon
function updateSortIcon(header, isAscending) {
    // Remove any existing icons from other headers
    const allHeaders = header.parentElement.querySelectorAll('th');
    allHeaders.forEach((head) => {
        const icon = head.querySelector('.sort-icon');
        if (icon) {
            icon.remove(); // Remove the icon from other headers
        }
    });

    // Add the appropriate sorting icon to the header
    const icon = document.createElement('img');
    icon.src = isAscending ? "/lokapustaka/img/sort-asc.png" : "/lokapustaka/img/sort-desc.png";
    icon.alt = "Sort Icon";
    icon.classList.add('sort-icon');
    header.appendChild(icon); // Append the icon to the clicked header
}

//===============Execute Function===============//
window.onload = function () {
    setActiveBasedOnURL();
    initializeSortableTables();
};

setInterval(updateTime, 60000);