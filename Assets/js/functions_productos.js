var base_url = "http://localhost/AltoVoltaje";
let tableProductos;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    tableProductos = $('#tableProductos').DataTable({
        "ajax": {
            "url": base_url + "/Productos/getProductos",
            "dataSrc": "data",
            "error": function(xhr, error, code) {
                console.error('DataTables AJAX error:', error, code);
                console.error('Response:', xhr.responseText);
            }
        },
        "columns": [
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return `<input type="checkbox" class="product-checkbox" value="${row.idProducto}">`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    const imageSrc = row.Imagen_Producto ? 
                        `${base_url}/Assets/images/uploads/${row.Imagen_Producto}` : 
                        `${base_url}/Assets/images/product-placeholder.png`;
                    
                    return `
                        <div class="product-cell">
                            <img src="${imageSrc}" alt="${row.Nombre_Producto}" class="product-image" 
                                 onerror="this.src='${base_url}/Assets/images/product-placeholder.png'">
                            <div class="product-info">
                                <div class="product-name">${row.Nombre_Producto}</div>
                                <div class="product-id">ID: ${row.SKU || row.idProducto}</div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                "data": "Precio_Venta",
                "render": function(data, type, row) {
                    return `<div class="price-cell">$${parseFloat(data || 0).toFixed(2)}</div>`;
                }
            },
            {
                "data": "Stock_Actual",
                "render": function(data, type, row) {
                    return `<div class="quantity-cell">${data || 0}</div>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    // Simulate sales data based on product info
                    const sales = Math.floor(Math.random() * 2000) + 100;
                    const maxSales = 2000;
                    const percentage = (sales / maxSales) * 100;
                    
                    let progressClass = 'low';
                    if (percentage > 70) progressClass = 'high';
                    else if (percentage > 40) progressClass = 'medium';
                    
                    return `
                        <div class="sales-cell">
                            <div class="sales-info">
                                <div class="sales-number">${sales} Sales</div>
                                <div class="sales-progress">
                                    <div class="sales-progress-bar ${progressClass}" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return `
                        <div class="action-buttons">
                            <button class="btn-action btn-edit" onclick="btnEditProducto(${row.idProducto})" title="Edit Product">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="btnDelProducto(${row.idProducto})" title="Delete Product">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        "scrollX": true,
        "scrollY": false,
        "responsive": false,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[1, "asc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });

    // Custom search functionality
    $('#searchProducts').on('keyup', function() {
        tableProductos.search(this.value).draw();
    });

    // Load filter options
    loadFilterOptions();
});



function loadFilterOptions() {
    // Load categories for filter
    let request = new XMLHttpRequest();
    request.open("GET", base_url + "/Productos/getProductos", true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.data && objData.data.length > 0) {
                populateFilters(objData.data);
            }
        }
    }
}

function populateFilters(products) {
    const categorySelect = document.getElementById('filterCategory');
    const categories = [...new Set(products.map(p => p.Nombre_Categoria).filter(c => c))];
    
    categorySelect.innerHTML = '<option value="">All Categories</option>';
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = category;
        categorySelect.appendChild(option);
    });
}

function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    const isVisible = panel.style.display !== 'none';
    
    if (isVisible) {
        panel.style.display = 'none';
        panel.classList.remove('show');
    } else {
        panel.style.display = 'grid';
        panel.classList.add('show');
    }
}

function clearFilters() {
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    applyFilters();
}

function applyFilters() {
    const category = document.getElementById('filterCategory').value;
    const status = document.getElementById('filterStatus').value;
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    
    // Apply filters using DataTables API
    tableProductos.columns().search('').draw();
    
    if (category) {
        tableProductos.column(1).search(category, false, false);
    }
    
    // Apply custom filtering logic here for price range
    tableProductos.draw();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function exportProducts() {
    // Get selected products or all if none selected
    const selected = [];
    document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
        selected.push(cb.value);
    });
    
    if (selected.length === 0) {
        swal("Export", "Please select products to export or use Export All functionality.", "info");
        return;
    }
    
    // Implement export functionality
    swal("Export", `Exporting ${selected.length} products...`, "success");
}

function openModal() {
    document.querySelector('#modalFormProducto').reset();
    document.querySelector('#idProducto').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Save";
    document.querySelector('.modal-title').innerHTML = "New Product";
    document.querySelector('#titleModal').innerHTML = "New Product";
    $('#modalFormProducto').modal('show');
}

function btnEditProducto(idproducto) {
    document.querySelector('#titleModal').innerHTML = "Update Product";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Update";
    
    let request = new XMLHttpRequest();
    let ajaxUrl = base_url + '/Productos/getProducto/' + idproducto;
    request.open("GET", ajaxUrl, true);
    request.send();
    
    request.onreadystatechange = function() {
        if(request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);
            if(objData.status) {
                // Populate form fields
                document.querySelector("#idProducto").value = objData.data.idProducto;
                document.querySelector("#txtNombre").value = objData.data.Nombre_Producto;
                document.querySelector("#txtSKU").value = objData.data.SKU;
                document.querySelector("#txtPrecio").value = objData.data.Precio_Venta;
                document.querySelector("#txtStock").value = objData.data.Stock_Actual;
                document.querySelector("#txtDescripcion").value = objData.data.Descripcion_Producto;
                
                $('#modalFormProducto').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function btnDelProducto(idproducto) {
    swal({
        title: "Delete Product",
        text: "Do you really want to delete this product?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            let request = new XMLHttpRequest();
            let ajaxUrl = base_url + '/Productos/delProducto';
            let strData = "idProducto=" + idproducto;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            
            request.onreadystatechange = function() {
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status) {
                        swal("Deleted!", objData.msg, "success");
                        tableProductos.ajax.reload();
                    } else {
                        swal("Attention!", objData.msg, "error");
                    }
                }
            }
        }
    });
}

// Form submission handler
if(document.querySelector("#formProducto")) {
    let formProducto = document.querySelector("#formProducto");
    formProducto.onsubmit = function(e) {
        e.preventDefault();
        
        let intIdProducto = document.querySelector('#idProducto').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strSKU = document.querySelector('#txtSKU').value;
        let strPrecio = document.querySelector('#txtPrecio').value;
        let strStock = document.querySelector('#txtStock').value;
        let strDescripcion = document.querySelector('#txtDescripcion').value;
        
        if(strNombre == '' || strSKU == '' || strPrecio == '' || strStock == '') {
            swal("Attention", "All fields are required.", "error");
            return false;
        }
        
        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + '/Productos/setProducto';
        let formData = new FormData(formProducto);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        
        request.onreadystatechange = function() {
            if(request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                if(objData.status) {
                    $('#modalFormProducto').modal("hide");
                    formProducto.reset();
                    swal("Products", objData.msg, "success");
                    tableProductos.ajax.reload();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
}