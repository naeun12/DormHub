<template>
    <NotificationList ref="toastRef" />
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class="p-4 mt-4 mb-3">
        <!-- Search Bar -->
        <div class="mb-3 w-100">
            <div class="input-group shadow-sm rounded-pill overflow-hidden" style="border: 1px solid #4edce2;">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search Tenant's name"
                    aria-label="Search Tenant" v-model="searchTerm" @input="searchTenants" />
            </div>
        </div>


        <!-- Filters & Buttons Row -->
        <div class="d-flex flex-column flex-md-row flex-wrap gap-3 align-items-stretch">
            <!-- Dorm & Date Pickers -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Dorm Select -->
                <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                    style="border: 1px solid #4edce2; min-width: 250px; width: 100%;">
                    <select id="dormSelect" class="form-select border-0 shadow-none w-100" v-model="selectedDormId"
                        @change="filterDorms">
                        <option value="" disabled>Select Dorm</option>
                        <option value="all">All Dorms</option>
                        <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                            {{ dorm.dormName }} (ID: {{ dorm.dormID }})
                        </option>
                    </select>
                </div>
                <!-- Date Picker -->
                <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                    style="border: 1px solid #4edce2; min-width: 200px; width: 100%;">
                    <input type="date" class="form-control border-0 shadow-none w-100" v-model="startDate"
                        @change="filterByDate" />
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="ms-md-auto d-flex flex-wrap gap-2 justify-content-end w-100">
                <button
                    class="btn btn-success px-3 py-2 shadow-sm fw-semibold d-flex align-items-center gap-2 w-100 w-md-auto"
                    @click="viewMoveInTenants">
                    <i class="bi bi-house-check fs-5"></i>
                    <span class="text-nowrap">Confirmed Move-in Tenant</span>
                </button>
                <button
                    class="btn btn-primary px-3 py-2 shadow-sm fw-semibold d-flex align-items-center gap-2 w-100 w-md-auto"
                    @click="downloadReport">
                    <i class="bi bi-file-earmark-text fs-5"></i>
                    <span class="text-nowrap">Generate Active Tenant Report</span>
                </button>
                <button
                    class="btn btn-secondary px-3 py-2 shadow-sm fw-semibold d-flex align-items-center gap-2 w-100 w-md-auto"
                    @click="extensionReport">
                    <i class="bi bi-file-earmark-text fs-5"></i>
                    <span class="text-nowrap">Generate Extension Payment Report</span>
                </button>
            </div>
        </div>


        <!-- Confirmed Move-in Modal -->
        <div v-if="clickConfirmedMoveInTenantModal" class="modal d-block modal-lg modal-dialog-centered" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="modal-header bg-info text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold">
                            <i class="bi bi-house-door me-2"></i> Confirmed Move-in Tenant
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="clickConfirmedMoveInTenantModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden"
                            style="border: 1px solid #4edce2;">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-none"
                                placeholder="Search Tenants name" v-model="searchMoveIn" @input="searchMoveInTenant" />
                        </div>
                        <div v-if="moveInTenants.length > 0">
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3"
                                v-for="moveInTenant in moveInTenants" :key="moveInTenant.approvedID">
                                <div class="position-relative d-flex justify-content-center align-items-center">
                                    <img :src="moveInTenant.pictureID" alt="Tenant Image" class="card-img-top"
                                        style="height: 200px; width: 100%; object-fit: cover;" />
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2 px-2 py-1 shadow">
                                        <i class="bi bi-check-circle me-1"></i> {{ moveInTenant.source_type }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-info mb-2">
                                        <i class="bi bi-person-circle me-2"></i> Tenant Information
                                    </h6>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Name:</strong> {{ moveInTenant.firstname }}
                                            {{ moveInTenant.lastname }}</li>
                                        <li class="list-group-item"><strong>Email:</strong> {{ moveInTenant.contactEmail
                                            }}</li>
                                        <li class="list-group-item"><strong>Phone:</strong> {{
                                            moveInTenant.contactNumber }}</li>
                                    </ul>
                                    <h6 class="fw-bold text-info mb-2">
                                        <i class="bi bi-door-closed me-2"></i> Room Information
                                    </h6>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item"><strong>Dorm:</strong> {{
                                            moveInTenant.room?.dorm?.dormName }}</li>
                                        <li class="list-group-item"><strong>Room Number:</strong> {{
                                            moveInTenant.room?.roomNumber }}</li>
                                        <li class="list-group-item"><strong>Room Type:</strong> {{
                                            moveInTenant.room?.roomType }}</li>
                                        <li class="list-group-item"><strong>Move-in Date:</strong> {{
                                            formatDate(moveInTenant.moveInDate) }}</li>
                                    </ul>
                                    <div class="d-grid">
                                        <button class="btn btn-success fw-semibold shadow-sm"
                                            @click="moveInTenantBTN(moveInTenant.approvedID)">
                                            <i class="bi bi-house-door me-1"></i> Move in
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0 shadow-sm">
                                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                            <a class="page-link" href="#"
                                                @click.prevent="viewMoveInTenants(currentPage - 1)">« Previous</a>
                                        </li>
                                        <li v-for="page in totalPages" :key="page" class="page-item"
                                            :class="{ active: currentPage === page }">
                                            <a class="page-link" href="#" @click.prevent="viewMoveInTenants(page)">{{
                                                page }}</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                            <a class="page-link" href="#"
                                                @click.prevent="viewMoveInTenants(currentPage + 1)">Next »</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div v-else class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                            <span>No tenants found</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- No Tenants Message -->
        <div v-if="!tenants.length" class="d-flex flex-column justify-content-center align-items-center"
            style="height: 200px;">
            <i class="bi bi-emoji-frown mb-2" style="font-size: 2rem; color: #6c757d;"></i>
            <p class="text-muted fw-bold">No Tenant found.</p>
        </div>


        <!-- TENANT LIST - NOW RESPONSIVE -->
        <div class="container-fluid bg-white rounded shadow-sm mt-4 px-1 px-md-3"
            style="border:1px solid #4edce2; max-height: 700px; overflow-y: auto;">
            <!-- Desktop Header -->
            <div class="d-none d-md-flex fw-bold bg-info text-white text-center py-2 rounded-top">
                <div class="col-1">#</div>
                <div class="col-2 text-start ps-3">Tenant Name</div>
                <div class="col-3 text-start ps-3">Email</div>
                <div class="col-2 text-start ps-3">Dorm Name</div>
                <div class="col-1">Room No</div>
                <div class="col-2">Status</div>
                <div class="col-1">Actions</div>
            </div>


            <!-- Tenant Rows -->
            <div v-for="tenant in tenants" :key="tenant.approvedID">
                <!-- Desktop Row -->
                <div class="d-none d-md-flex text-center align-items-center border-bottom py-2">
                    <div class="col-1">{{ tenant.approvedID }}</div>
                    <div class="col-2 text-start ps-3">{{ tenant.firstname }} {{ tenant.lastname }}</div>
                    <div class="col-3 text-start ps-3">{{ tenant.contactEmail }}</div>
                    <div class="col-2 text-start ps-3">{{ tenant.room?.dorm?.dormName ?? 'N/A' }}</div>
                    <div class="col-1">{{ tenant.room?.roomNumber ?? 'N/A' }}</div>
                    <div class="col-2">
                        <span class="badge rounded-pill px-3 py-2" :class="{
                            'bg-success text-white': tenant.status === 'active',
                            'bg-secondary text-white': tenant.status === 'moved_out',
                            'bg-warning text-dark': tenant.status === 'pending_moveout',
                            'bg-info text-white': tenant.status === 'transferring',
                        }">
                            {{ tenant.status?.replace('_', ' ').toUpperCase() }}
                        </span>
                    </div>
                    <div class="col-1 d-flex justify-content-center">
                        <button class="btn btn-sm btn-outline-primary"
                            @click="displaytenantInformation(tenant.approvedID)" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger ms-2" @click="softDelete(tenant)" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>


                <!-- Mobile Card -->
                <div class="d-md-none card border rounded-3 mb-2 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-1 fw-bold">{{ tenant.firstname }} {{ tenant.lastname }}</h6>
                            <span class="badge rounded-pill px-2 py-1" :class="{
                                'bg-success text-white': tenant.status === 'active',
                                'bg-secondary text-white': tenant.status === 'moved_out',
                                'bg-warning text-dark': tenant.status === 'pending_moveout',
                                'bg-info text-white': tenant.status === 'transferring',
                            }">
                                {{ tenant.status?.replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>
                        <p class="mb-1"><strong>Email:</strong> {{ tenant.contactEmail }}</p>
                        <p class="mb-1"><strong>Dorm:</strong> {{ tenant.room?.dorm?.dormName ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Room:</strong> {{ tenant.room?.roomNumber ?? 'N/A' }}</p>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button class="btn btn-sm btn-outline-primary"
                                @click="displaytenantInformation(tenant.approvedID)" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" @click="softDelete(tenant)" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tenant Info Modal -->
        <div v-if="VisibleTenantModal" class="modal fade show d-block rounded-3" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <div class="modal-header bg-info border-bottom-0">
                        <h5 class="modal-title text-white fw-bold">🧾 Tenants Information</h5>
                        <button type="button" class="btn-close text-white" @click="VisibleTenantModal = false"></button>
                    </div>
                    <div class="modal-body px-3 px-md-5">
                        <div class="text-center mb-4">
                            <img :src="selectedtenant.pictureID"
                                class="rounded-circle border border-3 border-light shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;" />
                            <p class="mt-3">
                                <span class="badge rounded-pill px-3 py-2 fs-6" :class="{
                                    'bg-success text-white': tenantStatus === 'active',
                                    'bg-secondary text-white': tenantStatus === 'moved_out',
                                    'bg-warning text-dark': tenantStatus === 'pending_moveout',
                                    'bg-info text-white': tenantStatus === 'transferring',
                                }">
                                    {{ tenantStatus?.replace('_', ' ').toUpperCase() }}
                                </span>
                            </p>
                        </div>
                        <div class="row g-3 g-md-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-person-fill me-2"></i>First Name</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.firstname" />
                                    <p v-if="errors.firstname" class="text-danger small mt-1">{{ errors.firstname[0] }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-123 me-2"></i>Age</label>
                                    <input type="number" class="form-control" v-model="selectedtenant.age" />
                                    <p v-if="errors.age" class="text-danger small mt-1">{{ errors.age[0] }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                                    <input type="email" class="form-control" v-model="selectedtenant.contactEmail" />
                                    <p v-if="errors.contactEmail" class="text-danger small mt-1">{{
                                        errors.contactEmail[0] }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-building me-2"></i> Dorm Name</label>
                                    <select v-model="selectedDormId" class="form-select">
                                        <option disabled value="">Select Dorm</option>
                                        <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                                            {{ dorm.dormName }}
                                        </option>
                                    </select>
                                    <div v-if="tenantStatus === 'transferring'" class="mt-2">
                                        <button class="btn btn-primary w-100 rounded-3" @click="onDormChange()">Change
                                            Room</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-geo-alt-fill me-2"></i>Dorm
                                        Address</label>
                                    <input type="text" class="form-control" :value="selectedDorm.address" readonly />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-calendar-x me-2"></i> Move-In Date</label>
                                    <input type="text" class="form-control"
                                        :value="formatDate(selectedtenant.moveInDate)" readonly />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-door-closed-fill me-2"></i>Room
                                        Number</label>
                                    <input type="text" class="form-control" :value="selectedtenant?.room?.roomNumber"
                                        readonly />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-person-fill me-2"></i>Last Name</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.lastname" />
                                    <p v-if="errors.lastname" class="text-danger small mt-1">{{ errors.lastname[0] }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-gender-ambiguous me-2"></i>Gender</label>
                                    <select class="form-select" v-model="selectedtenant.gender">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <p v-if="errors.gender" class="text-danger small mt-1">{{ errors.gender[0] }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-telephone-fill me-2"></i>Contact
                                        Number</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.contactNumber" />
                                    <p v-if="errors.contactNumber" class="text-danger small mt-1">{{
                                        errors.contactNumber[0] }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-house-door-fill me-2"></i>Room
                                        Type</label>
                                    <input type="text" class="form-control" :value="selectedtenant?.room?.roomType"
                                        readonly />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-currency-dollar me-2"></i>Monthly
                                        Rent</label>
                                    <input type="text" class="form-control" :value="'₱ ' + selectedtenant.room?.price"
                                        readonly />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-calendar-x me-2"></i> Move-Out
                                        Date</label>
                                    <input type="text" class="form-control"
                                        :value="formatDate(selectedtenant.moveOutDate)" readonly />
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label"><i
                                            class="bi bi-person-check-fill me-2"></i>Status</label>
                                    <select v-model="selectedtenant.status" id="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="moved_out">Moved Out</option>
                                        <option value="pending_moveout">Pending Move Out</option>
                                        <option value="transferring">Transferring</option>
                                    </select>
                                </div>
                            </div>
                            <StatusAlert :status="tenantStatus" role="Landlord" />
                        </div>
                    </div>
                    <div
                        class="modal-footer justify-content-between bg-light border-top-0 px-3 px-md-4 py-3 flex-column flex-md-row gap-2">
                        <button class="btn btn-outline-primary w-100 w-md-auto" @click="messagePage(selectedtenant)">💬
                            Message Tenant</button>
                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <button v-if="selectedtenant.notifyRent === 0" @click="notifyTenant(selectedtenant)"
                                class="btn btn-outline-success w-100">
                                📩 Notify Tenant about Rent Extension
                            </button>
                            <button v-else-if="selectedtenant.notifyRent === 1"
                                @click="btnViewTenantPaymentModal(selectedtenant)"
                                class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-receipt"></i>
                                <span>View Tenant Payment</span>
                            </button>
                        </div>
                        <button class="btn btn-outline-secondary w-100 w-md-auto" @click="updateTenantInformation">
                            Update Tenant Information
                        </button>
                    </div>
                </div>
            </div>
            <Toastcomponents ref="toast" />
        </div>


        <!-- Room Modal -->
        <div v-if="selectedRoomModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <div class="modal-header text-dark">
                        <h5 class="modal-title fw-bold">Room Details</h5>
                        <button type="button" class="btn-close btn-close-dark"
                            @click="selectedRoomModal = false"></button>
                    </div>
                    <div class="modal-body bg-light rounded p-3">
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-door-closed me-2"></i>Select Room</label>
                            <select class="form-select shadow-sm w-100" @change="onchangeRoomDetails"
                                v-model="selectedRoomId">
                                <option disabled value="">Select Room</option>
                                <option v-for="room in rooms" :key="room.roomID" :value="room.roomID">
                                    Room {{ room.roomNumber }} - {{ room.roomType }} - {{ room.availability }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3 text-dark text-center fw-bold p-2 rounded shadow-sm bg-white"
                            style="border-left: 5px solid #0d6efd;">
                            🏠 Room Details
                        </div>
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Room ID</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.roomID"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dorm ID</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.fkdormID"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Room Number</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.roomNumber"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Room Type</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.roomType"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Monthly Rent</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.price" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Availability</label>
                                        <div>
                                            <span v-if="roomsdetails.availability === 'Available'"
                                                class="badge bg-success p-2">Available</span>
                                            <span v-else class="badge bg-danger p-2">Occupied</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Furnishing Status</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.furnishing_status"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Listing Type</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.listingType"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Area (Sqm)</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.areaSqm"
                                            readonly />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gender Preference</label>
                                        <input type="text" class="form-control" v-model="roomsdetails.genderPreference"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light" v-if="isButtonChangingRoom">
                        <button type="button" class="btn btn-primary px-4" @click="updateRoom(roomsdetails.roomID)">
                            <i class="bi bi-save me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tenant Payment Modal -->
        <div v-if="viewtenantpaymentModal" class="modal d-block modal-lg modal-dialog-centered" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="modal-header bg-info text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold">
                            <i class="bi bi-house-door me-2"></i> Tenant Extension Payment
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="viewtenantpaymentModal = false"></button>
                    </div>
                    <div class="modal-body p-3 p-md-4">
                        <div v-if="tenantpayment.paymentOption === 'online'" class="p-2 p-md-4">
                            <h5 class="fw-semibold text-primary mb-3"><i class="bi bi-wallet2 me-2"></i> Online Payment
                            </h5>
                            <p class="small text-muted mb-3">The tenant has submitted an online payment. Please review
                                the details and verify.</p>
                            <div class="text-center mb-3">
                                <img :src="tenantpayment?.payments?.[0]?.paymentImage || 'default-placeholder.png'"
                                    alt="Payment Proof" class="img-fluid rounded shadow-sm border"
                                    style="max-height: 300px; object-fit: contain;" />
                                <p class="mt-2 small text-muted">Payment Screenshot / GCash Receipt</p>
                            </div>
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <p><i class="bi bi-person-circle text-primary me-2"></i> <strong>Tenant
                                            Name:</strong> {{ tenantpayment.firstname }} {{ tenantpayment.lastname }}
                                    </p>
                                    <p><i class="bi bi-currency-dollar text-success me-2"></i> <strong>Amount:</strong>
                                        ₱{{ tenantpayment?.payments?.[0]?.amount || 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i
                                        class="bi bi-calendar-plus text-primary me-2"></i> Extend Date</label>
                                <input type="date" v-model="extensionDate"
                                    :min="tenantpayment.moveOutDate.split(' ')[0]" class="form-control" />
                                <small class="text-muted">Please select a new date later than the current move-out
                                    date.</small>
                            </div>
                            <div class="d-grid gap-2 d-md-flex">
                                <button class="btn btn-success flex-fill"
                                    @click="updateOnlinePayment(tenantpayment?.payments?.[0], tenantpayment.approvedID, 'approved')">
                                    <i class="bi bi-check-circle me-2"></i> Approve Payment
                                </button>
                                <button class="btn btn-danger flex-fill"
                                    @click="updateOnlinePayment(tenantpayment?.payments?.[0], tenantpayment.approvedID, 'rejected')">
                                    <i class="bi bi-x-circle me-2"></i> Reject Payment
                                </button>
                            </div>
                        </div>


                        <div v-if="tenantpayment.paymentOption === 'onsite'" class="p-2 p-md-4">
                            <h5 class="fw-semibold mb-3"><i class="bi bi-cash-coin me-2 text-success"></i> Onsite
                                Payment</h5>
                            <div class="card shadow-sm border-0 rounded-4 mb-3">
                                <div class="card-body">
                                    <p><i class="bi bi-person-fill text-primary me-2"></i> <strong>Tenant Name:</strong>
                                        {{ tenantpayment.firstname }} {{ tenantpayment.lastname }}</p>
                                    <p><i class="bi bi-currency-exchange text-success me-2"></i> <strong>Room Monthly
                                            Rate:</strong> ₱{{ tenantpayment?.room?.price }}</p>
                                    <p><i class="bi bi-calendar-check-fill text-info me-2"></i> <strong>Move-out
                                            Date:</strong> {{ formatDate(tenantpayment.moveOutDate) }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i
                                        class="bi bi-calendar-plus text-primary me-2"></i> Extend Date</label>
                                <input type="date" v-model="extensionDate"
                                    :min="tenantpayment.moveOutDate.split(' ')[0]" class="form-control" />
                                <small class="text-muted">Please select a new date later than the current move-out
                                    date.</small>
                            </div>
                            <div class="alert alert-warning mt-3 rounded-3 d-flex align-items-start">
                                <i class="bi bi-info-circle-fill fs-5 me-2 mt-1"></i>
                                <span>The tenant will pay <strong>personally</strong> to the landlord. (The landlord
                                    will verify the payment)</span>
                            </div>
                            <button class="btn btn-success w-100"
                                @click="updateExtension(tenantpayment.approvedID)">Update Extension</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Modalconfirmation ref="modal" />
</template>

<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import { debounce } from 'lodash';
import NotificationList from '@/components/notifications.vue';
import StatusAlert from '@/components/approveTenantsAlert.vue';
import { update } from 'lodash';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,
        StatusAlert

    },
    data() {
        return {
            searchTerm: '',
            selectedDormId: '',
            selectedRoomNumber: '',
            selectedRoomId: '',
            currentRoomID: '',
            currentTenantID: '',
            isButtonChangingRoom: false,
            isButtonChangingDorm: false,
            tenantStatus: '',
            selectedRoomModal: false,
            dorms: [],
            rooms: [],
            errors: {},
            roomsdetails: [],
            selectedroomNumber: '',
            tenants: [],
            selectedtenant: {
                firstname: "",
                lastname: "",
                gender: "",
                age: "",
                contactEmail: "",
                contactNumber: "",
                status: ""
            }, VisibleTenantModal: false,
            notifications: [],
            hasSubscribed: false,
            receiverID: '',
            landlord_id: '',
            searchTimeout: null,
            clickConfirmedMoveInTenantModal: false,
            moveInTenants: [],
            currentPage: 1,
            totalPages: 1,
            searchMoveInTenantTimeout: null,
            searchMoveIn: '',
            viewtenantpaymentModal: false,
            tenantpayment: [],
            extensionDate: '',
            startDate: this.getTodayDate(), // set default to today
        }
    },
    watch:
    {
        'selectedtenant.room.dorm.dormID'(newVal) {
            if (newVal) {
                this.selectedDormId = newVal;
            }
        },
        searchTerm(newValue) {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.searchTenants(newValue);
            }, 500); // 3 seconds delay
        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.landlord_id;
            Echo.private(`notifications.${this.receiverID}`)
                .subscribed(() => {
                    console.log('✔ Subscribed!');
                })
                .listen('.NewNotificationEvent', (e) => {
                    this.notifications.unshift(e); // save for list
                    this.$refs.toastRef.pushNotification({
                        title: e.title || 'New Notification',
                        message: e.message,
                        color: 'success',
                    });
                });
        },

        async getDormId() {
            const res = await axios.get(`/get-dorms/${this.landlord_id}`);
            this.dorms = res.data.dorms;
            console.log(this.dorms);
        },
        async getTenantList() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/tenants-list', { withCredentials: true });
                this.tenants = response.data.tenant;
            } catch (error) {
                console.error("Error fetching tenant list:", error.response?.data || error.message);
            } finally {
                this.$refs.loader.loading = false;

            }
        },

        async displaytenantInformation(tenantID) {
            this.errors = {};

            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/tenants-view/${tenantID}`);
                if (response.data.status === 'success') {
                    this.selectedtenant = response.data.tenant;
                    this.currentRoomID = response.data.tenant?.room.roomID;
                    this.currentTenantID = response.data.tenant.approvedID;
                    this.tenantStatus = response.data.tenant.status;
                    this.VisibleTenantModal = true;
                }

            } catch (error) {
                console.error('Error fetching reservation details:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async updateTenantInformation() {
            try {

                const confirmed = await this.$refs.modal.show({
                    title: 'Update Tenant Information',
                    message: `Are you sure you want to update this tenant's information?`,
                    functionName: 'Update Tenant Info (Optional)'
                });
                if (!confirmed) {
                    this.rules.pop();
                    return;
                }
                this.$refs.loader.loading = true;

                const response = await axios.put(`/tenants-update/${this.selectedtenant.approvedID}`, this.selectedtenant);
                if (response.data.status === 'success') {
                    this.VisibleTenantModal = false;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.errors = {};
                    this.getTenantList();

                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Laravel validation errors
                    this.errors = error.response.data.errors;
                } else {
                    this.$refs.toast.showToast("Something went wrong.", 'danger');
                }
                console.error('Error updating tenant information:', error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },

        onDormChange() {
            this.$refs.loader.loading = true;
            this.isButtonChangingRoom = false;
            axios.get('/get/rooms', {
                params: { dormID: this.selectedDormId }
            })
                .then(response => {
                    if (response.data.status === 'success') {
                        this.rooms = response.data.data;
                        this.selectedRoomModal = true;
                        this.$refs.loader.loading = false;
                        this.roomsdetails = [];
                        this.selectedRoomId = '';


                    }
                })
                .catch(error => {
                    console.error('Error fetching rooms:', error);
                    this.$refs.loader.loading = false;

                });
        },
        async onchangeRoomDetails() {
            this.$refs.loader.loading = true;
            this.isButtonChangingRoom = true;
            axios.get('/get/roomsdetails', {
                params: { roomID: this.selectedRoomId }
            })
                .then(response => {
                    if (response.data.status === 'success') {
                        this.roomsdetails = response.data.data[0] || {};
                        this.$refs.loader.loading = false;

                    }
                })
                .catch(error => {
                    this.$refs.loader.loading = false;

                    console.error('Error fetching rooms:', error);
                });
        },
        async updateRoom(newRoomID) {
            this.errors = {}; // clear previous errors

            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Change Tenant Room',
                    message: `Are you sure you want to move this tenant to another room?`,
                    functionName: 'Change Tenant Room (Optional)'
                });
                if (!confirmed) {
                    this.rules.pop();
                    return;
                }
                this.$refs.loader.loading = true;
                const res = await axios.put(`/tenant/room/update/${newRoomID}`, {
                    current_room_id: this.currentRoomID,
                    tenant_id: this.currentTenantID
                });
                if (res.data.status === 'success') {
                    this.selectedRoomModal = false;
                    this.VisibleTenantModal = false;
                    this.$refs.toast.showToast(res.data.message, 'success');
                    this.getTenantList();
                }
                else if (res.data.status === 'error') {
                    this.$refs.toast.showToast(res.data.message, 'danger');
                    this.selectedRoomModal = false;
                    this.VisibleTenantModal = false;
                }


            } catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async searchTenants() {
            try {
                this.$refs.loader.loading = true;
                this.selectedDormId = '';
                if (this.searchTerm === '') {
                    this.getTenantList();
                    return; // stop execution kung walay gi‑type


                }

                const res = await axios.get(`/tenants/search`, {
                    params: { query: this.searchTerm }
                });
                this.tenants = res.data.tenants;

            } catch (error) {
                console.error("Error searching tenants:", error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async filterDorms() {
            try {
                this.$refs.loader.loading = true;
                // Kung "all" ang gipili, kuhaon tanan tenants
                if (this.selectedDormId === 'all') {
                    this.getTenantList();
                    return;
                }

                // Fetch tenants filtered by dorm ID
                const res = await axios.get(`/tenants/filter-by-dorm`, {
                    params: { dorm_id: this.selectedDormId }
                });

                if (res.data.status === 'success') {
                    this.tenants = res.data.tenants;
                    this.searchTerm = '';

                } else {
                    this.tenants = [];
                }
            } catch (error) {
                console.error("Error filtering tenants:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        messagePage(selectedReservation) {
            const tenantID = selectedReservation.fktenantID;

            if (!tenantID) {
                alert('No tenant assigned to this reservation.');
                return;
            }

            const url = `/api/select/landlord/conversations/${this.landlord_id}?tenant_id=${tenantID}`;
            window.location.href = url;
        },
        async notifyTenant(selectedtenant) {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Confirm Rent Extension',
                    message: `Are you sure you want to notify this tenant about their rent extension?`,
                    functionName: 'Send Notification'
                });

                if (!confirmed) {
                    if (this.rules && this.rules.length > 0) {
                        this.rules.pop();
                    }
                    return;
                }
                this.$refs.loader.loading = true;
                const response = await axios.post('/notify/tenant', {
                    landlordID: this.landlord_id,
                    approveID: selectedtenant.approvedID
                });

                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.VisibleTenantModal = false;

                } else {
                    this.$refs.toast.showToast('Failed to send notification.', 'error');
                    this.VisibleTenantModal = false;

                }
            }
            catch (error) {
                console.error(error);
                this.$refs.toast.showToast('Something went wrong while sending the notification.', 'error');
            }
            finally {
                this.$refs.loader.loading = false;
            }
        },
        async viewMoveInTenants(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/add/movein/tenant', {
                    params: { page }
                });

                if (response.data.status === 'success') {
                    this.clickConfirmedMoveInTenantModal = true;
                    this.moveInTenants = response.data.moveInTenant.data; // tenants
                    this.currentPage = response.data.moveInTenant.current_page;
                    this.totalPages = response.data.moveInTenant.last_page;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async searchMoveInTenant() {
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get('/search/movein/tenant', {
                    params: {
                        searchMoveIn: this.searchMoveIn
                    }
                });

                if (response.data.status === 'success') {
                    this.moveInTenants = response.data.tenants.data;
                } else {
                    this.moveInTenants = [];
                }
            } catch (error) {
                console.error("Error searching move-in tenants:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async moveInTenantBTN(approvedID) {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Confirm Move-In',
                    message: `Are you sure you want to move in this tenant?`,
                    functionName: 'Move In Tenant'
                });
                if (confirmed === false) {
                    if (this.rules && this.rules.length > 0) {
                        this.rules.pop();
                    }
                    return;
                }
                this.$refs.loader.loading = true;
                const response = await axios.get('/movein/tenant', {
                    params: { approvedID }
                });
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.clickConfirmedMoveInTenantModal = false;
                    this.getTenantList();
                } else if (response.data.status === 'error') {
                    this.$refs.toast.showToast(response.data.message, 'danger');
                    this.clickConfirmedMoveInTenantModal = false;
                } else {
                    this.$refs.toast.showToast('Failed to move in tenant.', 'danger');
                }

            } catch (error) {
                console.error("Error moving in tenant:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async btnViewTenantPaymentModal(selectedtenant) {
            try {
                const res = await axios.get(`/view/tenant/payment/${selectedtenant.approvedID}`);
                this.$refs.loader.loading = true;
                this.tenantpayment = res.data.tenant;
                this.extensionDate = this.tenantpayment.moveOutDate;
                this.viewtenantpaymentModal = true;
            } catch (error) {
                console.error("Error viewing tenant payment:", error);
            }
            finally {
                this.$refs.loader.loading = false;
            }

        },
        async updateExtension(approveID) {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Confirm Extension Update',
                    message: `Are you sure you want to update the extension date to ${this.extensionDate.split(' ')[0]}?`,
                    functionName: 'Update Extension'
                });

                if (confirmed) {
                    this.$refs.loader.loading = true;

                    const response = await axios.post(`/update/extension/${approveID}`, {
                        extensionDate: this.extensionDate
                    });

                    if (response.data.status === 'success') {
                        this.$refs.toast.showToast(response.data.message, 'success');
                        this.viewtenantpaymentModal = false;
                        this.VisibleTenantModal = false;

                        this.getTenantList();
                    } else {
                        this.$refs.toast.showToast('Failed to update extension.', 'error');
                    }
                } else {
                    if (this.rules && this.rules.length > 0) {
                        this.rules.pop();
                    }
                    return;
                }
            }
            catch (error) {
                console.error("Error updating extension:", error);
            }
            finally {
                this.$refs.loader.loading = false;
            }
        },
        async updateOnlinePayment(tenantPayment, approvedID, status) {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Confirm Payment {}'.replace('{}', status.charAt(0).toUpperCase() + status.slice(1)),
                    message: `Are you sure you want to ${status} this payment?`,
                    functionName: `${status.charAt(0).toUpperCase() + status.slice(1)} Payment`
                });

                if (confirmed) {
                    this.$refs.loader.loading = true;

                    const response = await axios.post(`/update/tenant/payment/extension/${approvedID}`, {
                        status: status,
                        paymentID: tenantPayment,
                        extensionDate: this.extensionDate

                    });

                    if (response.data.status === 'success') {
                        this.$refs.toast.showToast(response.data.message, 'success');
                        this.viewtenantpaymentModal = false;
                        this.VisibleTenantModal = false;

                        this.getTenantList();
                    } else {
                        this.$refs.toast.showToast('Failed to approve payment.', 'error');
                    }
                } else {
                    if (this.rules && this.rules.length > 0) {
                        this.rules.pop();
                    }
                    return;
                }
            }
            catch (error) {
                console.error("Error approving payment:", error);
            }
            finally {
                this.$refs.loader.loading = false;
            }
        },
        async softDelete(tenant) {
            const confirmed = await this.$refs.modal.show({
                title: 'Delete Tenant',
                message: `Are you sure you want to delete this tenant? This action cannot be undone.`,
                functionName: 'Delete Tenant'
            });
            try {
                if (confirmed) {
                    this.$refs.loader.loading = true;

                    const response = await axios.post(`/soft-delete/tenant/${tenant.approvedID}`);

                    if (response.data.status === 'success') {
                        this.$refs.toast.showToast(response.data.message, 'success');
                        this.getTenantList();

                    } else {
                        this.$refs.toast.showToast('Failed to delete tenant.', 'error');
                    }
                } else {
                    if (this.rules && this.rules.length > 0) {
                        this.rules.pop();
                    }
                    return;
                }
            }
            catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;
            }

        },
        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        getTodayDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        },
        nextDay(date) {
            if (!date) return null;
            let d = new Date(date);
            d.setDate(d.getDate() + 1);
            return d.toISOString().split('T')[0]; // format YYYY-MM-DD
        },
        downloadReport() {
            const dateParam = this.startDate || this.getTodayDate(); // fallback to today
            window.open(`/report/active-tenants?date=${dateParam}`, '_blank');
        },
        extensionReport() {
            const dateParam = this.startDate || this.getTodayDate();
            window.open(`/report/extension-payments?date=${dateParam}`, '_blank');
        },


    },

    mounted() {
        this.landlord_id = document.getElementById('tenantpage').dataset.landlordId;

        this.subscribeToNotifications();
        this.getTenantList();
        if (this.selectedtenant?.room?.dorm?.dormID) {
            this.selectedDormId = this.selectedtenant.room.dorm.dormID;
        }

        this.getDormId();

    },
    computed:
    {

        selectedDorm() {
            return this.dorms.find(d => d.dormID === this.selectedDormId) || {};
        },

        filteredRooms() {
            const dorm = this.selectedDorm;
            return dorm ? dorm.rooms : [];
        },

    },


}
</script>