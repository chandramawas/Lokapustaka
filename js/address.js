// HTML Elements
const provinceSelect = document.getElementById('province');
const regencySelect = document.getElementById('regency');
const districtSelect = document.getElementById('district');
const villageSelect = document.getElementById('village');

const regencyContainer = document.getElementById('regency_container');
const districtContainer = document.getElementById('district_container');
const villageContainer = document.getElementById('village_container');

// Fetch and Populate Options
async function fetchData(url, targetSelect) {
    try {
        const response = await fetch(url);
        const data = await response.json();

        // Add new options
        data.forEach(item => {
            const option = document.createElement('option');
            option.setAttribute('data-id', item.id);  // Use custom attribute 'data-id' to store the id
            option.value = item.name;
            option.textContent = item.name;  // Display item.name in the dropdown
            targetSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

// Populate Provinces on Load
fetchData('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json', provinceSelect);

// Fetch and Populate Regencies on Province Change
provinceSelect.addEventListener('change', () => {
    const provinceId = provinceSelect.options[provinceSelect.selectedIndex].getAttribute('data-id');  // Get 'data-id' attribute of the selected option

    // Reset regency, district, and village options
    regencySelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';
    districtSelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';
    villageSelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';

    // Fetch new regencies based on selected province
    fetchData(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`, regencySelect);

    // Hide district and village containers until they are populated
    districtContainer.style.display = 'none';
    villageContainer.style.display = 'none';

    // Show regency container
    regencyContainer.style.display = 'block';
});

// Fetch and Populate Districts on Regency Change
regencySelect.addEventListener('change', () => {
    const regencyId = regencySelect.options[regencySelect.selectedIndex].getAttribute('data-id');  // Get 'data-id' attribute of the selected option

    // Reset district and village options
    districtSelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';
    villageSelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';

    // Fetch new districts based on selected regency
    fetchData(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`, districtSelect);

    // Show district container
    districtContainer.style.display = 'block';
    villageContainer.style.display = 'none';
});

// Fetch and Populate Villages on District Change
districtSelect.addEventListener('change', () => {
    const districtId = districtSelect.options[districtSelect.selectedIndex].getAttribute('data-id');  // Get 'data-id' attribute of the selected option

    // Reset village options
    villageSelect.innerHTML = '<option value="" disabled selected>Pilih...</option>';

    // Fetch new villages based on selected district
    fetchData(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`, villageSelect);

    // Show village container
    villageContainer.style.display = 'block';
});