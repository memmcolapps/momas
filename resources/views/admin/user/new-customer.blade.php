@extends('layouts.main')
@section('content')

<style>
.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.search-dropdown-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.search-dropdown-item:hover {
    background-color: #f8f9fa;
}

.search-dropdown-item:last-child {
    border-bottom: none;
}

.selected-meter {
    margin-top: 8px;
    padding: 8px;
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.clear-selection {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #666;
    padding: 0 5px;
}

.clear-selection:hover {
    color: #000;
}

.no-results {
    padding: 10px;
    color: #666;
    font-style: italic;
}
</style>


    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Add New Customer</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-customer" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Customer Information</h6>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">First Name</label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Phone</label>
                                        <input type="number" name="phone" class="form-control" required>
                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Estate</label>
                                        <select type="text" name="estate_id" class="form-control" required>
                                            <option value="1">None</option>
                                            @foreach($estate as $data)
                                                <option value="{{$data->id}}">{{$data->title}}</option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Address</label>
                                        <input type="text" name="address" class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">City</label>
                                        <input type="text" name="city" class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">State</label>
                                        <select type="text" name="state" class="form-control" >
                                            <option disabled selected>--Select State--</option>
                                            <option value="Abia">Abia</option>
                                            <option value="Adamawa">Adamawa</option>
                                            <option value="Akwa Ibom">Akwa Ibom</option>
                                            <option value="Anambra">Anambra</option>
                                            <option value="Bauchi">Bauchi</option>
                                            <option value="Bayelsa">Bayelsa</option>
                                            <option value="Benue">Benue</option>
                                            <option value="Borno">Borno</option>
                                            <option value="Cross River">Cross River</option>
                                            <option value="Delta">Delta</option>
                                            <option value="Ebonyi">Ebonyi</option>
                                            <option value="Edo">Edo</option>
                                            <option value="Ekiti">Ekiti</option>
                                            <option value="Enugu">Enugu</option>
                                            <option value="FCT">Federal Capital Territory</option>
                                            <option value="Gombe">Gombe</option>
                                            <option value="Imo">Imo</option>
                                            <option value="Jigawa">Jigawa</option>
                                            <option value="Kaduna">Kaduna</option>
                                            <option value="Kano">Kano</option>
                                            <option value="Katsina">Katsina</option>
                                            <option value="Kebbi">Kebbi</option>
                                            <option value="Kogi">Kogi</option>
                                            <option value="Kwara">Kwara</option>
                                            <option value="Lagos">Lagos</option>
                                            <option value="Nasarawa">Nasarawa</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Ogun">Ogun</option>
                                            <option value="Ondo">Ondo</option>
                                            <option value="Osun">Osun</option>
                                            <option value="Oyo">Oyo</option>
                                            <option value="Plateau">Plateau</option>
                                            <option value="Rivers">Rivers</option>
                                            <option value="Sokoto">Sokoto</option>
                                            <option value="Taraba">Taraba</option>
                                            <option value="Yobe">Yobe</option>
                                            <option value="Zamfara">Zamfara</option>
                                        </select>

                                    </div>
                                </div>


                                <hr class="my-4">

                                <div class="row">
                                    <h6 class="d-flex justify-content-start my-2">Attach Meter</h6>
                                    <div class="col-6">
                                        <div class="col-xl-6 col-sm-12" style="position: relative;">
                                            <label class="my-2">Choose Meter</label>
                                            <input type="text" 
                                                   name="meter_search" 
                                                   id="meterSearchInput" 
                                                   placeholder="Select estate first" 
                                                   class="form-control" 
                                                   autocomplete="off"
                                                   disabled>

                                            <!-- Hidden input to store selected meter ID -->
                                            <input type="hidden" name="meterid" id="selectedMeterId" required>

                                            <!-- Search results dropdown -->
                                            <div id="meterSearchResults" class="search-dropdown" style="display: none;"></div>

                                            <!-- Selected meter display -->
                                            <div id="selectedMeterDisplay" class="selected-meter" style="display: none;">
                                                <span class="meter-info"></span>
                                                <button type="button" class="clear-selection" onclick="clearMeterSelection()">×</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <h6 class="d-flex justify-content-start my-2">Attach Meter</h6> -->
                                    <!-- <div class="col-6"> -->
                                        <!-- <div class="col-xl-3 col-sm-12" style="position: relative;"> -->
<!--  -->
                                            <!-- <label class="my-2">Choose Meter</label> -->
                                            <!-- <select name="meterid" id="meterSelect" class="form-control" required disabled> -->
                                                <!-- <option value="">Select Estate First</option> -->
                                            <!-- </select> -->
                                            <!-- <input type="text" name="meterNo" id="searchMeter" placeholder="Type meter number..." class="form-control" required autocomplete="off">

                                            <div id="meterResult" class="search-result"></div> -->

                                            <script>

                                                // // Originally this script written by the contractors handles dynamic meter searching based on estate selection.
                                                // // However, it has been commented out in favor of a simpler approach using a dropdown that popul

                                                // document.addEventListener('DOMContentLoaded', function () {
                                                //     const estateSelect = document.querySelector('[name="estate_id"]');
                                                //     const searchMeterInput = document.getElementById('searchMeter');

                                                //     // Initially disable the searchMeter input
                                                //     searchMeterInput.disabled = true;

                                                //     // Enable/disable searchMeter based on estate selection
                                                //     estateSelect.addEventListener('change', function () {
                                                //         if (this.value) {
                                                //             searchMeterInput.disabled = false; // Enable if an estate is selected
                                                //         } else {
                                                //             searchMeterInput.disabled = true; // Disable if no estate is selected
                                                //         }
                                                //     });

                                                //     // Add search functionality
                                                //     searchMeterInput.addEventListener('keyup', function () {
                                                //         let query = this.value;
                                                //         console.log('User input:', query);

                                                //         // Get the selected estate ID
                                                //         let estateId = estateSelect.value;

                                                //         if (query.length > 2 && estateId) {
                                                //             let xhr = new XMLHttpRequest();
                                                //             xhr.open('GET', `/search-meters?q=${query}&estate_id=${estateId}`, true);

                                                //             xhr.onreadystatechange = function () {
                                                //                 if (xhr.readyState == 4 && xhr.status == 200) {
                                                //                     console.log('Response received:', xhr.responseText);

                                                //                     let meters = JSON.parse(xhr.responseText);
                                                //                     let meterResultDiv = document.getElementById('meterResult');
                                                //                     meterResultDiv.innerHTML = ''; // Clear previous results

                                                //                     if (meters.length > 0) {
                                                //                         meters.forEach(meter => {
                                                //                             let div = document.createElement('div');
                                                //                             div.textContent = meter.meterNo;
                                                //                             div.setAttribute('data-id', meter.id);

                                                //                             div.addEventListener('click', function () {
                                                //                                 searchMeterInput.value = meter.meterNo;
                                                //                                 meterResultDiv.style.display = 'none'; // Hide suggestions
                                                //                             });

                                                //                             meterResultDiv.appendChild(div);
                                                //                         });
                                                //                         meterResultDiv.style.display = 'block'; // Show results
                                                //                     } else {
                                                //                         let noResultDiv = document.createElement('div');
                                                //                         noResultDiv.textContent = 'No meters found';
                                                //                         noResultDiv.style.color = 'red';
                                                //                         meterResultDiv.appendChild(noResultDiv);
                                                //                         meterResultDiv.style.display = 'block'; // Show the "No meters found" message
                                                //                     }
                                                //                 } else if (xhr.readyState == 4) {
                                                //                     console.log('Error: Status', xhr.status); // Log error status
                                                //                 }
                                                //             };

                                                //             xhr.onerror = function () {
                                                //                 console.error('Request error'); // Log any request errors
                                                //             };

                                                //             xhr.send();
                                                //         } else if (query.length <= 2) {
                                                //             document.getElementById('meterResult').style.display = 'none'; // Hide if input is too short
                                                //         }
                                                //     });
                                                // });

                                             </script>


                                        <!-- </div> -->
                                    <!-- </div> -->

                                </div>



                                <div class="row">


                                    <hr class="my-4">
                                    <h6 class="d-flex justify-content-start my-2">Login Information</h6>

                                    <div class="col-4">
                                        <label class="my-2">Customer Role</label>
                                        <select type="text" name="role" class="form-control" required>
                                            <option value="2">Customer</option>
                                            <option value="1">Admin</option>
                                            <option value="3">Estate Admin</option>
                                            <option value="4">Estate Staff</option>


                                        </select>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Password</label>
                                        <input type="password" name="password" value="123456" class="form-control" required>
                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Confirm Password</label>
                                        <input type="password" name="password_confirmation" value="123456" class="form-control" required>
                                    </div>



                                </div>

                                <hr class="my-4">
                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Create
                                </button>




                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div> <!-- container-fluid -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const estateSelect = document.querySelector('[name="estate_id"]');
    const meterSearchInput = document.getElementById('meterSearchInput');
    const meterSearchResults = document.getElementById('meterSearchResults');
    const selectedMeterId = document.getElementById('selectedMeterId');
    const selectedMeterDisplay = document.getElementById('selectedMeterDisplay');
    
    let searchTimeout;
    let currentEstateId = null;
    
    // For Super Admin - enable search when estate is selected
    if (estateSelect) {
        estateSelect.addEventListener('change', function() {
            currentEstateId = this.value;
            clearMeterSelection();
            
            if (currentEstateId && currentEstateId !== "1") {
                meterSearchInput.disabled = false;
                meterSearchInput.placeholder = "Type to search meters...";
            } else {
                meterSearchInput.disabled = true;
                meterSearchInput.placeholder = "Select estate first";
            }
        });
    } else {
        // For Estate Admin - search is always enabled
        currentEstateId = '{{Auth::user()->estate_id ?? ""}}';
    }
    
    // Search functionality
    meterSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hideSearchResults();
            return;
        }
        
        // Debounce search requests
        searchTimeout = setTimeout(() => {
            searchMeters(query);
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!meterSearchInput.contains(e.target) && !meterSearchResults.contains(e.target)) {
            hideSearchResults();
        }
    });
    
    function searchMeters(query) {
        if (!currentEstateId) return;
        
        // Show loading
        meterSearchResults.innerHTML = '<div class="search-dropdown-item">Searching...</div>';
        meterSearchResults.style.display = 'block';
        
        const url = `/admin/search-unassigned-meters?q=${encodeURIComponent(query)}&estate_id=${currentEstateId}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data.meters || []);
            })
            .catch(error => {
                console.error('Search error:', error);
                meterSearchResults.innerHTML = '<div class="no-results">Error searching meters</div>';
            });
    }
    
    function displaySearchResults(meters) {
        meterSearchResults.innerHTML = '';
        
        if (meters.length === 0) {
            meterSearchResults.innerHTML = '<div class="no-results">No meters found</div>';
            meterSearchResults.style.display = 'block';
            return;
        }
        
        meters.forEach(meter => {
            const item = document.createElement('div');
            item.className = 'search-dropdown-item';
            item.innerHTML = `
                <strong>${meter.meterNo}</strong>
                ${meter.AccountNo ? `<br><small>Account: ${meter.AccountNo}</small>` : ''}
            `;
            
            item.addEventListener('click', function() {
                selectMeter(meter);
            });
            
            meterSearchResults.appendChild(item);
        });
        
        meterSearchResults.style.display = 'block';
    }
    
    function selectMeter(meter) {
        // Set hidden input
        selectedMeterId.value = meter.id;
        
        // Clear search input
        meterSearchInput.value = '';
        
        // Show selected meter
        selectedMeterDisplay.querySelector('.meter-info').innerHTML = `
            Selected: <strong>${meter.meterNo}</strong>
            ${meter.AccountNo ? ` (${meter.AccountNo})` : ''}
        `;
        selectedMeterDisplay.style.display = 'flex';
        
        // Hide search results
        hideSearchResults();
    }
    
    function hideSearchResults() {
        meterSearchResults.style.display = 'none';
    }
    
    // Global function for clear button
    window.clearMeterSelection = function() {
        selectedMeterId.value = '';
        selectedMeterDisplay.style.display = 'none';
        meterSearchInput.value = '';
        hideSearchResults();
    }
});
</script>


    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Add New Customer</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-customer" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Customer Information</h6>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">First Name</label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Phone</label>
                                        <input type="number" name="phone" class="form-control" required>
                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Estate</label>
                                        <input type="text" value="{{$estate->title}}" class="form-control" disabled required>
                                        <input type="text" value="{{$estate->id}}" name="estate_id"  hidden>

                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Address</label>
                                        <input type="text" name="address" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">City</label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">State</label>
                                        <select type="text" name="state" class="form-control" required>
                                            <option disabled selected>--Select State--</option>
                                            <option value="Abia">Abia</option>
                                            <option value="Adamawa">Adamawa</option>
                                            <option value="Akwa Ibom">Akwa Ibom</option>
                                            <option value="Anambra">Anambra</option>
                                            <option value="Bauchi">Bauchi</option>
                                            <option value="Bayelsa">Bayelsa</option>
                                            <option value="Benue">Benue</option>
                                            <option value="Borno">Borno</option>
                                            <option value="Cross River">Cross River</option>
                                            <option value="Delta">Delta</option>
                                            <option value="Ebonyi">Ebonyi</option>
                                            <option value="Edo">Edo</option>
                                            <option value="Ekiti">Ekiti</option>
                                            <option value="Enugu">Enugu</option>
                                            <option value="FCT">Federal Capital Territory</option>
                                            <option value="Gombe">Gombe</option>
                                            <option value="Imo">Imo</option>
                                            <option value="Jigawa">Jigawa</option>
                                            <option value="Kaduna">Kaduna</option>
                                            <option value="Kano">Kano</option>
                                            <option value="Katsina">Katsina</option>
                                            <option value="Kebbi">Kebbi</option>
                                            <option value="Kogi">Kogi</option>
                                            <option value="Kwara">Kwara</option>
                                            <option value="Lagos">Lagos</option>
                                            <option value="Nasarawa">Nasarawa</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Ogun">Ogun</option>
                                            <option value="Ondo">Ondo</option>
                                            <option value="Osun">Osun</option>
                                            <option value="Oyo">Oyo</option>
                                            <option value="Plateau">Plateau</option>
                                            <option value="Rivers">Rivers</option>
                                            <option value="Sokoto">Sokoto</option>
                                            <option value="Taraba">Taraba</option>
                                            <option value="Yobe">Yobe</option>
                                            <option value="Zamfara">Zamfara</option>
                                        </select>

                                    </div>
                                </div>


                                <hr class="my-4">

                                <div class="row">


                                    <!-- <h6 class="d-flex justify-content-start my-2">Attach Meter</h6> -->

                                    <!-- <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Choose Meter</label>
                                        <select type="text" name="meterid" class="form-control" required>
                                            <option value=" "> Select meter </option>
                                            @foreach($meters as $data)
                                                <option value="{{$data->id}}">{{$data->meterNo}}</option>
                                            @endforeach
                                        </select>
                                    </div> -->

                                    <div class="col-xl-6 col-sm-12" style="position: relative;">
                                        <label class="my-2">Choose Meter</label>
                                        <input type="text" 
                                               name="meter_search" 
                                               id="meterSearchInput" 
                                               placeholder="Type to search meters..." 
                                               class="form-control" 
                                               autocomplete="off">

                                        <!-- Hidden input to store selected meter ID -->
                                        <input type="hidden" name="meterid" id="selectedMeterId" required>

                                        <!-- Search results dropdown -->
                                        <div id="meterSearchResults" class="search-dropdown" style="display: none;"></div>

                                        <!-- Selected meter display -->
                                        <div id="selectedMeterDisplay" class="selected-meter" style="display: none;">
                                            <span class="meter-info"></span>
                                            <button type="button" class="clear-selection" onclick="clearMeterSelection()">×</button>
                                        </div>
                                    </div>


                                </div>

                                <hr class="my-4">


                                <div class="row">


                                    <hr class="my-4">
                                    <h6 class="d-flex justify-content-start my-2">Login Information</h6>

                                    <div class="col-4">
                                        <label class="my-2">Customer Role</label>
                                        <select type="text" name="role" class="form-control" required>
                                            <option value="2">Customer</option>
                                        </select>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Password</label>
                                        <input type="password" name="password" value="123456" class="form-control" required>
                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Confirm Password</label>
                                        <input type="password" name="password_confirmation" value="123456" class="form-control" required>


                                    </div>



                                </div>

                                <hr class="my-4">
                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Create
                                </button>




                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div> <!-- container-fluid -->

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const meterSearchInput = document.getElementById('meterSearchInput');
            const meterSearchResults = document.getElementById('meterSearchResults');
            const selectedMeterId = document.getElementById('selectedMeterId');
            const selectedMeterDisplay = document.getElementById('selectedMeterDisplay');

            let searchTimeout;
            let currentEstateId = '{{Auth::user()->estate_id ?? ""}}'; // Estate Admin always has their estate ID

            // Search functionality
            if (meterSearchInput) {
                meterSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        hideSearchResults();
                        return;
                    }

                    // Debounce search requests
                    searchTimeout = setTimeout(() => {
                        searchMeters(query);
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!meterSearchInput.contains(e.target) && !meterSearchResults.contains(e.target)) {
                        hideSearchResults();
                    }
                });
            }

            function searchMeters(query) {
                if (!currentEstateId) return;

                // Show loading
                meterSearchResults.innerHTML = '<div class="search-dropdown-item">Searching...</div>';
                meterSearchResults.style.display = 'block';

                const url = `/admin/search-unassigned-meters?q=${encodeURIComponent(query)}&estate_id=${currentEstateId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data.meters || []);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        meterSearchResults.innerHTML = '<div class="no-results">Error searching meters</div>';
                    });
            }

            function displaySearchResults(meters) {
                meterSearchResults.innerHTML = '';

                if (meters.length === 0) {
                    meterSearchResults.innerHTML = '<div class="no-results">No meters found</div>';
                    meterSearchResults.style.display = 'block';
                    return;
                }

                meters.forEach(meter => {
                    const item = document.createElement('div');
                    item.className = 'search-dropdown-item';
                    item.innerHTML = `
                        <strong>${meter.meterNo}</strong>
                        ${meter.AccountNo ? `<br><small>Account: ${meter.AccountNo}</small>` : ''}
                    `;

                    item.addEventListener('click', function() {
                        selectMeter(meter);
                    });

                    meterSearchResults.appendChild(item);
                });

                meterSearchResults.style.display = 'block';
            }

            function selectMeter(meter) {
                // Set hidden input
                selectedMeterId.value = meter.id;

                // Clear search input
                meterSearchInput.value = '';

                // Show selected meter
                selectedMeterDisplay.querySelector('.meter-info').innerHTML = `
                    Selected: <strong>${meter.meterNo}</strong>
                    ${meter.AccountNo ? ` (${meter.AccountNo})` : ''}
                `;
                selectedMeterDisplay.style.display = 'flex';

                // Hide search results
                hideSearchResults();
            }

            function hideSearchResults() {
                meterSearchResults.style.display = 'none';
            }

            // Global function for clear button
            window.clearMeterSelection = function() {
                selectedMeterId.value = '';
                selectedMeterDisplay.style.display = 'none';
                meterSearchInput.value = '';
                hideSearchResults();
            }
        });
    </script>

    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
    @endif


@endsection
