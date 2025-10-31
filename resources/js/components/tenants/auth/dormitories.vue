<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />


    <div class="container-fluid py-4 bg-light min-vh-100 d-flex flex-column flex-lg-row gap-4">
        <!-- AI Question Sidebar -->
        <aside class="ai-aside bg-white p-4 shadow-lg rounded-4 mx-auto d-flex flex-column"
            style="max-width: 500px; max-height: 150vh; overflow-y: auto;">
            <div class="container text-center flex-shrink-0">
                <h4 class="mb-4 fw-bold text-primary">💬 Looking for Rooms or Dorms? Ask AI!</h4>


                <!-- Input -->
                <div class="input-group input-group-lg mb-4 shadow-sm rounded-pill overflow-hidden border">
                    <input v-model="question" type="text" class="form-control border-0"
                        placeholder="Type your question here..." @keyup.enter="aiQuestion" />
                    <button @click="aiQuestion" class="btn btn-primary px-4 rounded-pill">
                        Ask
                    </button>
                </div>
            </div>


            <!-- Dorm Recommendations -->
            <div class="flex-grow-1" style="overflow-y: auto; padding-right: 5px;">
                <div class="card border-0 shadow-sm mb-4 ai-response">
                    <div class="card-body p-4">
                        💬 AI Response
                        <p class="mb-0 text-muted" style="white-space: pre-wrap; line-height: 1.5;">
                            {{ chatresponse || 'No response yet. Type your question above and hit Ask!' }}
                        </p>
                    </div>
                </div>
                <h5 class="card-title text-start mb-3 fw-semibold">AI Suggestions: Rooms & Dorms</h5>


                <div v-if="dormReccomend.length > 0" class="row g-3 justify-content-center">
                    <div v-for="(dorm, idx) in dormReccomend" :key="idx" class="col-12">
                        <div class="card dorm-card mb-4 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-3">
                                <h5 class="card-title fw-bold mb-2">{{ dorm.dormName }}</h5>
                                <p class="mb-1 text-muted"><i class="fas fa-user text-primary me-2"></i>{{
                                    dorm.occupancyType }}</p>
                                <p class="mb-2 text-muted"><i class="fas fa-map-marker-alt text-primary me-2"></i>{{
                                    dorm.address }}</p>


                                <!-- Rooms -->
                                <div v-if="dorm.rooms && dorm.rooms.length" class="rooms-scroll mb-2 border-top pt-2">
                                    <div v-for="(room, rIdx) in dorm.rooms" :key="rIdx" class="mb-2 pb-2 border-bottom">
                                        <p class="text-muted mb-1">
                                            Room #: {{ room.roomNumber }} | Type: {{ room.type }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            Price: ₱{{ room.price }} | Availability: {{ room.availability }}
                                        </p>
                                        <div v-if="room.features && room.features.length" class="mb-2">
                                            <strong class="d-block mb-1">Features:</strong>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span v-for="(feature, index) in room.features" :key="index"
                                                    class="badge bg-info text-dark rounded-pill">
                                                    {{ feature }}
                                                </span>
                                            </div>
                                        </div>






                                    </div>
                                </div>


                                <a class="btn-custom w-100 rounded-pill mt-3" @click="viewDormsDetails(dorm?.dormID)">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


                <p v-else class="text-muted mb-0">No dorms or rooms available.</p>
            </div>
        </aside>




        <!-- Main Content -->
        <div class="flex-grow-1" style="overflow-x: hidden;">
            <div class="container-fluid mt-3">
                <h2 class="mb-4 text-primary fw-semibold text-center">
                    Find Your Ideal Dorm in Mandaue and Lapu-Lapu City
                </h2>
            </div>


            <!-- Most Watched Dorms Horizontal Scroll -->
            <section class="text-primary mb-4">
                <h5 class="mb-3 fw-bold">Most Watched Dormitories</h5>
                <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 1rem;"
                    class="shadow-sm rounded-4 bg-light p-3">


                    <div class="d-flex flex-row gap-3" style="width: max-content;">
                        <div class="card dorm-card shadow-sm border-0 rounded-4 overflow-hidden"
                            v-for="(dorm, index) in mostwatchdorm" :key="index"
                            style="width: 20rem; flex-shrink: 0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                            <div class="position-relative">
                                <img :src="dorm?.images?.mainImage || dorm?.mainImage || 'https://via.placeholder.com/320x200'"
                                    class="card-img-top" :alt="dorm.dormName"
                                    style="height: 200px; object-fit: cover;" />
                                <span
                                    class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark rounded-pill shadow-sm px-3 py-2">
                                    <i class="bi bi-eye-fill me-1"></i>{{ dorm.views || 0 }}
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column" style="height: 150px;">
                                <h5 class="card-title text-primary fw-bold text-truncate">{{ dorm.dormName }}</h5>
                                <p class="card-text text-muted small mb-2 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ dorm.address || 'No description available.' }}
                                </p>
                                <div class="mt-auto">
                                    <button class="btn-custom rounded-pill w-100"
                                        @click="viewDormsDetails(dorm.dormID)">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Search Bar -->


            <!-- Filters -->
            <div class="py-4">


                <!-- Filter Header -->
                <div class="mb-4 p-4 rounded-3 shadow-sm bg-light text-start">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Optional Icon -->
                        <i class="bi bi-house-door-fill fs-1 text-primary"></i>


                        <div>
                            <h4 class="fw-bold mb-1" style="font-size: 1.75rem;">Find Dormitory Houses</h4>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                Filter by city, price range, occupancy type, amenities, rating, and more
                            </p>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-primary"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                        aria-label="Search Locations" v-model="searchQuery" @input="debouncedSearch" />
                </div>




                <!-- Filters -->
                <div class="d-flex flex-wrap justify-content-start gap-2 mb-4">


                    <!-- City Buttons -->
                    <button class="btn py-2 rounded-pill fw-semibold"
                        :class="selectedButtons === 'All' ? 'btn-primary text-white' : 'btn-outline-primary'"
                        @click="btnAllFilter">
                        All ({{ numberdorms }})
                    </button>


                    <button class="btn px-4 py-2 rounded-pill fw-semibold"
                        :class="selectedButtons === 'Lapu-Lapu' ? 'btn-primary text-white' : 'btn-outline-primary'"
                        @click="btnCityFilter('Lapu-Lapu')">
                        Lapu-Lapu City ({{ lapulapu_dorms }})
                    </button>


                    <button class="btn px-4 py-2 rounded-pill fw-semibold"
                        :class="selectedButtons === 'Mandaue' ? 'btn-primary text-white' : 'btn-outline-primary'"
                        @click="btnCityFilter('Mandaue')">
                        Mandaue City ({{ mandaue_dorms }})
                    </button>






                    <!-- Price Range -->
                    <div class="form-floating" style="min-width: 180px;">
                        <select class="form-select shadow-sm rounded-3" v-model="selectedPriceRange"
                            @change="dropdownPriceRecommendations">
                            <option disabled value="">Select Price Range</option>
                            <option value="all">All Prices</option>
                            <option value="0-500">₱0 - ₱500</option>
                            <option value="501-1000">₱501 - ₱1000</option>
                            <option value="1001-1500">₱1001 - ₱1500</option>
                            <option value="1501+">₱1501 and above</option>
                        </select>
                        <label>Price Range (per room)</label>
                    </div>


                    <!-- Occupancy Type -->
                    <div class="form-floating" style="min-width: 180px;">
                        <select class="form-select shadow-sm rounded-3" v-model="selectedOccupancyType"
                            @change="dropdownGenderRecommdations">
                            <option disabled value="">Select Occupancy Type</option>
                            <option value="all">All Types</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Mixed">Mixed</option>
                        </select>
                        <label>Occupancy Type</label>
                    </div>


                    <!-- Amenities -->
                    <div class="form-floating" style="min-width: 180px;">
                        <select class="form-select shadow-sm rounded-3" v-model="selectedAmenity"
                            @change="dropdownAmenities">
                            <option disabled value="">Select Amenity</option>
                            <option v-for="amenity in amenitiesList" :key="amenity.id" :value="amenity.id">
                                {{ amenity.aminityName }}
                            </option>
                        </select>
                        <label>Amenities</label>
                    </div>
                    <!-- Rating -->
                    <div class="form-floating" style="min-width: 180px;">
                        <select class="form-select shadow-sm rounded-3" v-model="selectedRating" @change="dropdownRate">
                            <option disabled value="">Select Rating</option>
                            <option value="all">All Ratings</option>
                            <option value="5">★★★★★ (5 Stars)</option>
                            <option value="4">★★★★☆ (4 Stars & up)</option>
                            <option value="3">★★★☆☆ (3 Stars & up)</option>
                            <option value="2">★★☆☆☆ (2 Stars & up)</option>
                            <option value="1">★☆☆☆☆ (1 Star & up)</option>
                        </select>
                        <label>Minimum Rating</label>
                    </div>


                    <!-- Sort By -->
                    <div class="form-floating" style="min-width: 180px;">
                        <select class="form-select shadow-sm rounded-3" v-model="sortBy" @change="sortDateDropDown">
                            <option disabled value="">Dormitories by Date</option>
                            <option value="new-old">Newest to Oldest</option>
                            <option value="old-new">Oldest to Newest</option>
                        </select>
                        <label>Sort Dormitories by Date</label>
                    </div>
                    <div class="form-floating" style="min-width: 180px;">
                        <select v-model="selectedAvailability" class="form-select" @change="getAvailability">
                            <option disabled value="">Select Availability</option>
                            <option value="all">All</option>
                            <option value="Available">Available Only</option>
                            <option value="Not Available">Not Available</option>
                        </select>
                        <label>Availability</label>
                    </div>
                </div>
            </div>


            <!-- Dorm Listings Grid -->
            <div class="row g-4">




                <div class="col-12 col-sm-6 col-md-4 col-lg-3" v-for="(dorm, dormID) in dormitories" :key="dormID">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden d-flex flex-column">
                        <div class="image-wrapper position-relative rounded-3 overflow-hidden shadow-sm">
                            <!-- Dorm Image -->
                            <img :src="dorm?.images?.mainImage || dorm?.mainImage" class="w-100" :alt="dorm.dormName"
                                style="object-fit: cover; height: 180px;" />


                            <!-- Dorm Ratings overlay -->
                            <div v-if="boolrate"
                                class="position-absolute top-0 end-0 bg-dark bg-opacity-50 text-warning px-2 py-1 rounded-start">
                                <i class="bi bi-star-fill me-1"></i> {{ dorm.rating_percentage }}%
                            </div>
                        </div>






                        <div class="card-body d-flex flex-column justify-content-between flex-grow-1">


                            <div>
                                <h5 class="card-title text-dark fw-bold">{{ dorm.dormName }}</h5>
                                <span class="badge w-25 d-flex align-items-center gap-1 shadow-sm"
                                    :class="dorm.availability === 'Available' ? 'bg-success' : 'bg-secondary'">
                                    <i
                                        :class="dorm.availability === 'Available' ? 'bi bi-check-circle-fill' : 'bi bi-dash-circle-fill'"></i>
                                    {{ dorm.availability }}
                                </span>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-person-fill"></i> {{ dorm.occupancyType }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt-fill"></i> {{ dorm.address }}
                                </p>
                                <div v-if="rooms.length > 0" class="mt-3 p-2 border-top">
                                    <strong>Room Details:</strong>
                                    <div class="small text-muted">
                                        <p class="mb-1"><i class="bi bi-cash-stack"></i> Price: {{ dorm.price }}</p>
                                        <p class="mb-1"><i class="bi bi-gender-ambiguous"></i> Gender: {{
                                            dorm.genderPreference }}</p>
                                        <p class="mb-1"><i class="bi bi-house-door-fill"></i> Type: {{ dorm.roomType }}
                                        </p>
                                        <p class="mb-1"><i class="bi bi-furniture-fill"></i> Furnishing: {{
                                            dorm.furnishing_status }}</p>
                                        <p class="mb-0"><i class="bi bi-check-circle"></i> Availability: {{
                                            dorm.availability }}</p>
                                    </div>
                                </div>
                                <div v-if="amenitieslength === true" class="mt-3 p-2 border-top">
                                    <strong>Amenities:</strong>
                                    <div class="small text-muted" style="max-height: 100px; overflow-y: auto;">
                                        <p class="mb-1" v-for="amenity in dorm.amenities" :key="amenity.id">
                                            <i class="bi bi-check-circle"></i> {{ amenity.aminityName }}
                                        </p>
                                    </div>
                                </div>










                            </div>
                            <div class="mt-4 d-flex justify-content-center">
                                <button class="btn-custom rounded-pill px-4 w-100"
                                    @click="viewDormsDetails(dorm.dormID)">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center shadow-sm rounded-3 bg-white p-2 gap-1">


                        <!-- Previous Button -->
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                            <a class="page-link text-primary fw-semibold bg-light rounded" href="#"
                                @click.prevent="goToPage(currentPage - 1)">
                                &laquo; Previous
                            </a>
                        </li>


                        <!-- Page Numbers -->
                        <li class="page-item" v-for="page in totalPages" :key="page"
                            :class="{ active: currentPage === page }">
                            <a class="page-link px-3 py-2"
                                :class="currentPage === page ? 'bg-primary text-white rounded shadow-sm' : 'bg-light text-dark rounded hover:bg-primary hover:text-white transition'"
                                href="#" @click.prevent="goToPage(page)">
                                {{ page }}
                            </a>
                        </li>


                        <!-- Next Button -->
                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                            <a class="page-link text-primary fw-semibold bg-light rounded" href="#"
                                @click.prevent="goToPage(currentPage + 1)">
                                Next &raquo;
                            </a>
                        </li>


                    </ul>
                </nav>






            </div>


        </div>


    </div>
</template>


<script>
import axios from 'axios';
import _ from 'lodash';
import debounce from 'lodash/debounce';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';


export default {
    components: {
        Loader,
        NotificationList,


    },
    name: 'ProductGrid',
    data() {
        return {
            dormitories: [],
            filteredDorms: [],
            searchQuery: '',
            selectedPriceRange: '',
            selectedOccupancyType: '',
            recommendations: [],
            recommendloading: false,
            isGenderBased: false,
            recommend: '',
            dorms: [],
            dormReccomend: [],
            question: '',
            chatresponse: '',
            mostwatchdorm: [],
            tenant_id: '',
            notifications: [],
            receiverID: '',
            //new filters
            lapulapu_dorms: [],
            rooms: [],
            numberdorms: [],
            currentFilter: null, // e.g. 'all', 'city', 'price', etc.
            currentFilterParams: {},
            mandaue_dorms: [],
            selectedAmenity: '',
            selectedRating: '',
            sortBy: '',
            selectedAvailability: '',
            currentPage: 1,
            totalPages: 1,
            itemsPerPage: 12,
            selectedButtons: '',
            amenitiesList: [],
            amenitieslength: false,
            boolrate: false,
        };
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;


            this.receiverID = this.tenant_id;
            Echo.private(`notifications.${this.tenant_id}`)
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
        //filter
        async btnAllFilter() {
            this.selectedAmenity = '';
            this.selectedRating = '';
            this.sortBy = '';
            this.selectedAvailability = '';
            this.selectedOccupancyType = '';
            this.selectedPriceRange = '';
            this.rooms = [];
            this.boolrate = false;
            this.amenitieslength = false;
            this.dormListingfetch();
        },
        async btnCityFilter(city, page = 1) {
            this.selectedButtons = city;

            this.currentFilter = 'city';
            this.currentFilterParams = { city };
            this.selectedAmenity = '';
            this.selectedRating = '';
            this.sortBy = '';
            this.selectedAvailability = '';
            this.selectedOccupancyType = '';
            this.selectedPriceRange = '';
            this.rooms = [];
            this.boolrate = false;
            this.amenitieslength = false;
            this.$refs.loader.loading = true;
            const response = await axios.get('/select-cities', {
                params: { city: this.selectedButtons, page: page, per_page: this.itemsPerPage }

            });
            this.currentPage = response.data.dorms.current_page;
            this.totalPages = response.data.dorms.last_page;
            this.dormitories = response.data.dorms.data;
            this.$refs.loader.loading = false;
        },


        async dropdownPriceRecommendations(page = 1) {
            this.selectedButtons = '';
            this.selectedAmenity = '';
            this.currentFilter = 'price';
            this.selectedRating = '';
            this.sortBy = '';
            this.selectedAvailability = '';
            this.selectedOccupancyType = '';
            this.$refs.loader.loading = true;
            this.rooms = [];
            this.boolrate = false;
            this.amenitieslength = false;


            const [min, max] = this.getPriceRange(this.selectedPriceRange);


            try {
                const response = await fetch('/pricerecommendations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        min_price: Number(min),
                        max_price: Number(max),
                        page: page,
                        per_page: this.itemsPerPage
                    })
                });


                const result = await response.json();


                if (result.status === 'success') {
                    this.dormitories = result.data.data;       // current page dorms
                    this.currentPage = result.data.current_page;
                    this.totalPages = result.data.last_page;
                    this.rooms = result.data.data;
                    console.log(this.rooms);
                }


            } catch (error) {
                console.error(error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },


        getPriceRange(range) {
            switch (range) {
                case '0-500': return [0, 500];
                case '501-1000': return [501, 1000];
                case '1001-1500': return [1001, 1500];
                case '1501+': return [1501, 999999];
                default: return [0, 999999]; // fallback
            }
        },
        async dropdownGenderRecommdations(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.selectedButtons = '';
                this.currentFilter = 'gender';

                this.selectedPriceRange = '';
                this.selectedAmenity = '';
                this.selectedRating = '';
                this.sortBy = '';
                this.selectedAvailability = '';
                this.rooms = [];
                this.boolrate = false;
                this.amenitieslength = false;
                this.$refs.loader.loading = true;


                const response = await fetch('gender-recommendations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        occupancy_type: this.selectedOccupancyType.toLowerCase(),
                        page: page,
                        per_page: this.itemsPerPage
                    }),

                });
                const result = await response.json();


                if (result.status === 'success') {
                    this.dormitories = result.recommendations.data; // only items
                    this.currentPage = result.recommendations.current_page;
                    this.totalPages = result.recommendations.last_page;
                }


            }
            catch (error) {
                this.$refs.loader.loading = false;
            }
            finally {
                this.$refs.loader.loading = false;


            }
        },
        async dropdownAmenities(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.selectedButtons = '';
                this.selectedRating = '';
                this.currentFilter = 'amenity';
                this.sortBy = '';
                this.selectedAvailability = '';
                this.selectedOccupancyType = '';
                this.selectedPriceRange = '';
                this.rooms = [];
                this.boolrate = false;
                this.$refs.loader.loading = true;
                const response = await axios.post('/get/amenities', {
                    amenities: Array.isArray(this.selectedAmenity) ? this.selectedAmenity : [this.selectedAmenity],
                    params: { page: page, per_page: this.itemsPerPage }
                });


                // Update dorms list and count
                this.dormitories = response.data.dorms.data;
                this.currentPage = response.data.dorms.current_page;
                this.totalPages = response.data.dorms.last_page;
                this.amenitieslength = true;


            } catch (error) {
                console.error("Error fetching amenities:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async dropdownRate(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.selectedButtons = '';
                this.selectedAmenity = '';
                this.sortBy = '';
                this.selectedAvailability = '';
                this.selectedOccupancyType = '';
                this.selectedPriceRange = '';
                this.rooms = [];
                this.amenitieslength = false;
                this.$refs.loader.loading = true;
                const response = await axios.get('/get/rate', {
                    params: { rating: this.selectedRating, page: page, per_page: this.itemsPerPage }
                });
                this.boolrate = true;
                this.dormitories = response.data.dorms.data;
                this.currentPage = response.data.dorms.current_page;
                this.totalPages = response.data.dorms.last_page;
            } catch (error) {
                console.error("Error fetching by rate:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async sortDateDropDown(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.currentFilter = 'sort';
                this.selectedButtons = '';
                this.selectedAmenity = '';
                this.selectedRating = '';
                this.selectedAvailability = '';
                this.selectedOccupancyType = '';
                this.selectedPriceRange = '';
                this.rooms = [];
                this.amenitieslength = false;
                this.boolrate = false;
                this.$refs.loader.loading = true;
                const response = await axios.get('/get/sortByDate', {
                    params: { sortBy: this.sortBy, page: page, per_page: this.itemsPerPage }
                });


                this.dormitories = response.data.dorms.data;
                this.currentPage = response.data.dorms.current_page;
                this.totalPages = response.data.dorms.last_page;


            } catch (error) {
                console.error("Error sorting by date:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async getAvailability(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.selectedButtons = '';
                this.currentFilter = 'availability';
                this.selectedAmenity = '';
                this.selectedRating = '';
                this.sortBy = '';
                this.selectedOccupancyType = '';
                this.selectedPriceRange = '';
                this.rooms = [];
                this.amenitieslength = false;
                this.boolrate = false;
                this.$refs.loader.loading = true;
                const response = await axios.get('/get/availability', {
                    params: { availability: this.selectedAvailability, page: page, per_page: this.itemsPerPage },


                });


                this.dormitories = response.data.dorms.data;
                this.currentPage = response.data.dorms.current_page;
                this.totalPages = response.data.dorms.last_page;


            } catch (error) {
                console.error("Error fetching by availability:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },


        async dormListingfetch(page = 1) {
            try {
                this.selectedButtons = 'All';
                this.$refs.loader.loading = true;
                const response = await axios.get('/list-dorms', {
                    params: { page: page, per_page: this.itemsPerPage },
                });
                this.currentPage = response.data.dorms.current_page;
                this.totalPages = response.data.dorms.last_page;
                this.dormitories = response.data.dorms.data;
                this.numberdorms = response.data.total_dorms;
                this.lapulapu_dorms = response.data.lapulapu_dorms;
                this.mandaue_dorms = response.data.mandaue_dorms;
                this.rooms = [];
                this.$refs.loader.loading = false;




            } catch (error) {
                console.error("Error fetching dorms:", error);
                this.$refs.loader.loading = false;


            }
        },
        viewDormsDetails(dormitoryId) {
            this.tenant_id = window.tenant_id;
            window.location.href = `/room-details/${dormitoryId}/${this.tenant_id}`;
        },



        async goToPage(page) {
            if (page < 1 || page > this.totalPages) return;


            // Always call the right filter function based on currentFilter
            switch (this.currentFilter) {
                case 'city':
                    await this.btnCityFilter(this.currentFilterParams.city, page);
                    break;
                case 'price':
                    await this.dropdownPriceRecommendations(page);
                    break;
                case 'gender':
                    await this.dropdownGenderRecommdations(page);
                    break;
                case 'amenity':
                    await this.dropdownAmenities(page);
                    break;
                case 'rating':
                    await this.dropdownRate(page);
                    break;
                case 'sort':
                    await this.sortDateDropDown(page);
                    break;
                case 'availability':
                    await this.getAvailability(page);
                    break;
                case 'search':
                    await this.searchLocations(page);
                    break;
                default:
                    await this.dormListingfetch(page);
            }


            this.currentPage = page; // Update currentPage for UI
        },
        async searchLocations(page = 1) {
            try {
                this.$refs.loader.loading = true;
                this.selectedButtons = '';
                this.currentFilter = 'search';
                this.selectedAmenity = '';
                this.selectedRating = '';
                this.sortBy = '';
                this.selectedAvailability = '';
                this.selectedOccupancyType = '';
                this.selectedPriceRange = '';
                this.rooms = [];
                this.amenitieslength = false;
                this.boolrate = false;


                this.$refs.loader.loading = true;
                if (!this.searchQuery.trim()) {


                    await this.dormListingfetch();  // re-fetch all dorms
                    return;
                }


                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/search-locations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        location: this.searchQuery,
                        page: page,
                        per_page: this.itemsPerPage
                    }),
                });


                const result = await response.json();
                if (result.status === "success") {
                    this.dormitories = result.recommendations.data;
                    this.currentPage = result.recommendations.current_page; // must access from recommendations
                    this.totalPages = result.recommendations.last_page;    // also from recommendations
                    this.$refs.loader.loading = false;




                } else {
                    console.error('Server responded with error:', result.message);
                    this.dormitories = [];
                    this.$refs.loader.loading = false;




                }
            } catch (err) {
                console.error('Search failed:', err);
                this.filteredDorms = [];
                this.$refs.loader.loading = false;


            }
        },
        async aiQuestion() {
            try {
                this.$refs.loader.loading = true;


                // Send the user question to Laravel AI route
                const response = await axios.post('ai/question/reccomendations', { question: this.question });


                // AI text response
                this.chatresponse = response.data.message || 'Walay tubag gikan sa AI';


                // AI recommendations (dorms or rooms)
                const recs = response.data.recommendations || [];


                this.dormReccomend = (recs || []).map(d => ({
                    dormID: d.dormID || 0,
                    dormName: d.dormName || 'Unnamed Dorm',
                    address: d.address || 'No address provided',
                    occupancyType: d.occupancyType || 'Mixed',
                    amenities: d.amenities || '',
                    rules: d.rules || [],
                    rooms: (d.rooms || []).map(r => ({
                        roomNumber: r.roomNumber || 'N/A',
                        type: r.type || r.roomType || 'Standard',
                        price: r.price || 'Contact landlord',
                        availability: r.availability || 'Unknown',
                        features: r.features
                            ? (Array.isArray(r.features)
                                ? r.features
                                : r.features.split(',').map(f => f.trim()))
                            : []
                    }))
                }));


                console.log('AI Recommendations:', this.dormReccomend);
                console.log('AI Response:', this.chatresponse);


            } catch (error) {
                console.error('Error sending AI question:', error);
                this.chatresponse = 'Naa’y error sa pagkuha og recommendations.';
                this.dormReccomend = [];
            } finally {
                this.$refs.loader.loading = false;
            }
        },









        async mostWatchDorm() {
            try {
                this.tenant_id = window.tenant_id;  // siguro naa ni sa global js
                const response = await axios.get(`/most/watched/dorm/${this.tenant_id}`);
                this.mostwatchdorm = response.data.dorm;
                // I-update imong UI with this.mostwatchdorm
                console.log('Most watched dorm:', this.mostwatchdorm);
            } catch (error) {
                console.error('Error fetching most watched dorm:', error);
            }
        }




    },
    created() {
        this.debouncedSearch = debounce(this.searchLocations, 500);
    },
    mounted() {
        this.dormListingfetch();
        this.mostWatchDorm();
        this.tenant_id = window.tenant_id;
        this.subscribeToNotifications();
        axios.get('/fetch-amenities').then(response => {
            this.amenitiesList = response.data.amenities;
        });
    },
};




</script>