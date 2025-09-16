<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Stitch Design</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <!-- CSS -->
  <link rel="stylesheet" href="absent.css" />
</head>
<body>

<div class="app">

  <header class="header">
    <div class="header-left">
      <div class="logo">
        <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
          <g clip-path="url(#clip0_6_319)">
            <path d="M8.57829 8.57829C5.52816 11.6284 3.451 15.5145 2.60947 19.7452C1.76794 23.9758 2.19984 28.361 3.85056 32.3462C5.50128 36.3314 8.29667 39.7376 11.8832 42.134C15.4698 44.5305 19.6865 45.8096 24 45.8096C28.3135 45.8096 32.5302 44.5305 36.1168 42.134C39.7033 39.7375 42.4987 36.3314 44.1494 32.3462C45.8002 28.361 46.2321 23.9758 45.3905 19.7452C44.549 15.5145 42.4718 11.6284 39.4217 8.57829L24 24L8.57829 8.57829Z" fill="currentColor"></path>
          </g>
          <defs>
            <clipPath id="clip0_6_319"><rect fill="white" height="48" width="48"></rect></clipPath>
          </defs>
        </svg>
      </div>
      <h1 class="site-title">Acme HR</h1>
    </div>
    <div class="header-right">
      <button class="icon-btn">
        <span class="material-symbols-outlined">notifications</span>
      </button>
      <div class="avatar" style='background-image:url("https://lh3.googleusercontent.com/aida-public/AB6AXuBM7nEdrA93oydF5Po6LT0PzoezyNlrSXEWOu9WbglYWYMGGg1K-ZuMfnKakhna-xTNA91chdBIr3FE6YrSYcFsSKZc8jRZRuvsSBQsAWTArAvS_-DLOT6X_HIM9n6RgtYTYWEzsD-fbbMadOdJ-65cGbuLzzAdNIpYoTsmiULxQ2SaCHuBh9yJffGN8p4lSYYRAprau1vu1pxBt5eKeQmeQYpJrXHXWnmX-4AJJ4cpdCWzHk80tqcv7sUulx8o1lDkgv1NTYzNMWE");'></div>
    </div>
  </header>

  <main class="main">
    <div class="container">

      <div class="title-row">
        <h2>Absence List</h2>
        <button class="btn-primary">
          <span class="material-symbols-outlined">add</span>
          <span>Add Absence</span>
        </button>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th><input type="checkbox" /></th>
              <th>Employee</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input type="checkbox" /></td>
              <td>Liam Harper</td>
              <td><span class="badge badge-green">Present</span></td>
              <td><a href="#">Edit</a></td>
            </tr>
            <tr>
              <td><input type="checkbox" /></td>
              <td>Olivia Bennett</td>
              <td><span class="badge badge-red">Absent</span></td>
              <td><a href="#">Edit</a></td>
            </tr>
            <tr>
              <td><input type="checkbox" /></td>
              <td>Noah Carter</td>
              <td><span class="badge badge-yellow">Sick</span></td>
              <td><a href="#">Edit</a></td>
            </tr>
            <tr>
              <td><input type="checkbox" /></td>
              <td>Emma Hayes</td>
              <td><span class="badge badge-blue">At School</span></td>
              <td><a href="#">Edit</a></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Add Absence</h3>
            <button class="close-btn"><span class="material-symbols-outlined">close</span></button>
          </div>

          <div class="modal-body">
            <div class="card">
              <h3>Select Employees</h3>
              <div class="checkbox-row">
                <input type="checkbox" id="employee-checkbox-modal" />
                <label for="employee-checkbox-modal">Select All</label>
              </div>
              <div class="grid">
                <div class="grid-item">
                  <input type="checkbox" id="liam-harper-modal" />
                  <label for="liam-harper-modal">Liam Harper</label>
                </div>
                <div class="grid-item">
                  <input type="checkbox" id="olivia-bennett-modal" />
                  <label for="olivia-bennett-modal">Olivia Bennett</label>
                </div>
              </div>
            </div>

            <div class="card">
              <h3>Mark Absence Status</h3>
              <div class="status-grid">
                <button>Present</button>
                <button class="active">Absent</button>
                <button>Sick</button>
                <button>At School</button>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn-secondary">Cancel</button>
            <button class="btn-primary">Save Changes</button>
          </div>
        </div>
      </div>

    </div>
  </main>

</div>

</body>
</html>
