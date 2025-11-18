<template>
    <NotificationList ref="toastRef" />

    <!-- PERSONALIZE MODAL -->
    <div v-if="afterpersonalized"
        class="welcome-section position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
        style="background-color: rgba(0,0,0,0.7); z-index: 1050; padding: 1rem;">

        <!-- Loading Spinner -->


        <!-- Welcome message -->
        <div v-if="showafterWelcome" class="text-center text-white p-4 rounded-4 welcome-card">
            <div class="d-flex gap-3 justify-content-center mb-3 animated-header">
                <h1 class="display-4 fw-bold text-white">Welcome to</h1>
                <h1 class="display-4 fw-bold text-info">DormHub!</h1>
            </div>

            <p class="lead mb-2 animated-text">
                You’ve chosen <span class="fw-bold">{{ preferredLocation }}</span> as your preferred location.
            </p>
            <p class="lead mb-2 animated-text" style="animation-delay: 0.3s">
                Your preferred budget is <span class="fw-bold">₱{{ preferredPrice }}</span>.
            </p>
            <p class="lead mt-3 animated-text" style="animation-delay: 0.8s">
                Let’s help you find the perfect dormitory!
            </p>
        </div>

    </div>

    <div v-if="showWelcome"
        class="welcome-section position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center"
        style="background-color: rgba(0,0,0,0.5); z-index: 1050;">

        <!-- Loading Spinner -->

        <!-- Welcome message -->
        <div v-if="showWelcome" class="text-center text-white">
            <div class="d-flex gap-3">
                <h1 class="display-4 fw-bold mb-3 text-white">Welcome to </h1>
                <h1 class="display-4 fw-bold mb-3 text-info">DormHub!</h1>

            </div>
            <p class="lead mb-2">Find your perfect dormitory and book your room hassle-free.</p>

            <!-- Sequential text labels -->
            <div class="sequential-text mt-4">
                <span v-if="showText[0]" class="fs-4 fw-bold me-3">Find Your Dorm</span>
                <span v-if="showText[1]" class="fs-4 fw-bold">Book Now</span>
            </div>
        </div>
    </div>

    <div v-if="!isPersonalized">
        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow-lg border-0">
                    <!-- Header -->
                    <div class="modal-header bg-gradient text-white rounded-top-4"
                        style="background: linear-gradient(135deg, #0dcaf0, #198754);">
                        <h5 class="modal-title fs-5 fw-bold text-black">
                            <i class="bi bi-gear-fill me-2"></i>Set Your Preferences
                        </h5>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <!-- Preferred Room Price -->
                        <div class="mb-4">
                            <label for="price" class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar me-1"></i>Preferred Room Price
                            </label>
                            <div class="mb-3">

                                <div
                                    class="input-group shadow-sm rounded-pill overflow-hidden border border-1 border-light">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="bi bi-cash-stack text-success"></i>
                                    </span>
                                    <input type="number" id="price" v-model.number="preferredPrice"
                                        class="form-control border-0" placeholder="Enter your budget" required
                                        style="box-shadow: none;">
                                </div>
                                <small class="text-muted">Set your preferred monthly budget for rooms.</small>
                            </div>

                        </div>

                        <!-- Preferred Location -->
                        <div class="mb-4">
                            <label for="location" class="form-label fw-semibold text-black">
                                <i class="bi bi-geo-alt-fill me-1"></i>Preferred Location
                            </label>
                            <div
                                class="input-group shadow-sm rounded-pill overflow-hidden border border-1 border-light">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-pin-map-fill text-primary"></i>
                                </span>
                                <select id="location" v-model="preferredLocation" class="form-select border-0" required
                                    style="box-shadow: none;">
                                    <option value="" disabled>Select Location</option>
                                    <option value="Mandaue">Mandaue</option>
                                    <option value="Lapu-Lapu">Lapu-Lapu</option>
                                </select>
                            </div>
                            <small class="text-muted">Choose the area you prefer for your room.</small>
                        </div>


                        <!-- Amenities -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-house-fill me-1"></i>Amenities
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-3 border rounded-3 bg-light"
                                style="max-height: 180px; overflow-y: auto;">
                                <button v-for="amenity in aminitiesList" :key="amenity.id" type="button"
                                    class="btn btn-sm rounded-pill px-3 py-1 shadow-sm"
                                    :class="preferredAmenities.includes(amenity.id) ? 'btn-success text-white' : 'btn-outline-secondary'"
                                    @click="toggleAmenity(amenity.id)">
                                    <i class="bi bi-check-circle me-1"
                                        v-if="preferredAmenities.includes(amenity.id)"></i>
                                    {{ amenity.aminityName }}
                                </button>
                            </div>
                        </div>

                        <!-- Room Features -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-grid-fill me-1"></i>Room Features
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-3 border rounded-3 bg-light"
                                style="max-height: 180px; overflow-y: auto;">
                                <button v-for="feature in featuresList" :key="feature.id" type="button"
                                    class="btn btn-sm rounded-pill px-3 py-1 shadow-sm"
                                    :class="preferredFeature.includes(feature.id) ? 'btn-success text-white' : 'btn-outline-danger'"
                                    @click="toggleFeature(feature.id)">
                                    <i class="bi bi-check-circle me-1" v-if="preferredFeature.includes(feature.id)"></i>
                                    {{ feature.featureName }}
                                </button>
                            </div>
                        </div>

                        <!-- Rules & Policies -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-card-checklist me-1"></i>Rules & Policies
                            </label>
                            <div class="d-flex flex-wrap gap-2 p-3 border rounded-3 bg-light"
                                style="max-height: 180px; overflow-y: auto;">
                                <button v-for="rule in rulesList" :key="rule.id" type="button"
                                    class="btn btn-sm rounded-pill px-3 py-1 shadow-sm"
                                    :class="preferredRules.includes(rule.id) ? 'btn-success text-white' : 'btn-outline-info'"
                                    @click="toggleRule(rule.id)">
                                    <i class="bi bi-check-circle me-1" v-if="preferredRules.includes(rule.id)"></i>
                                    {{ rule.rulesName }}
                                </button>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="d-flex justify-content-end mt-3">
                            <button @click="updateSubmitPersonalized" class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-check-circle me-1"></i>Save Preferences
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Navigation -->
    <div class="bg-white m-3 py-3 px-2 text-center shadow-sm border-custom rounded-4">
        <ul class="nav justify-content-center gap-3 flex-wrap">
            <li class="nav-item">
                <a href="#" @click="viewBooking" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check fs-5"></i>
                    <span>View Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewPayment" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin fs-5"></i>
                    <span>Next Payment</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewMyrooms" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-house-door fs-5"></i>
                    <span>My Rooms</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewReservation" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text fs-5"></i>
                    <span>My Reservations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewnotifications"
                    class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-bell fs-5"></i>
                    <span>Notifications</span>
                </a>
            </li>

        </ul>

    </div>



    <!-- Content Section -->
    <div class="container-fluid m-2 py-5">
        <div class="personalization-section p-3 bg-light rounded-4 shadow-sm mb-4 mx-3">

            <!-- Gender Preference -->

            <div class="mb-4">
                <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 1rem;"
                    class="shadow-sm rounded-4 bg-light p-3">

                    <div class="d-flex flex-row gap-3" style="width: max-content;">
                        <div class="card dorm-card shadow-sm border-0 rounded-4 overflow-hidden"
                            v-for="(dorm, index) in genderPersonalized" :key="index"
                            style="width: 20rem; flex-shrink: 0; transition: transform 0.2s ease, box-shadow 0.2s ease;">

                            <!-- Dorm Image -->
                            <div class="position-relative">
                                <!-- Dorm Image -->
                                <img :src="dorm?.images?.mainImage || dorm?.mainImage || 'https://via.placeholder.com/320x200'"
                                    class="card-img-top" :alt="dorm.dormName"
                                    style="height: 200px; object-fit: cover;" />

                                <!-- Price Label -->
                                <span
                                    class="position-absolute top-0 start-0 m-2 px-2 py-1 bg-primary text-white rounded"
                                    style="font-size: 0.9rem;">
                                    ₱ {{dorm?.rooms?.length ? Math.min(...dorm.rooms.map(r => r.price)) : 'N/A'}}

                                </span>
                            </div>


                            <div class="card-body d-flex flex-column" style="height: auto;">
                                <!-- Dorm Name -->
                                <h5 class="card-title text-primary fw-bold text-truncate">{{ dorm.dormName }}</h5>

                                <!-- Address -->
                                <p class="card-text text-muted small mb-2 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ dorm.address || 'No description available.' }}
                                </p>

                                <!-- Occupancy Type -->
                                <p class="mb-2">
                                    <span class="badge text-white rounded-pill px-2 py-1" :class="{
                                        'bg-primary': dorm.occupancyType.includes('Male'),
                                        'bg-pink': dorm.occupancyType.includes('Female'),
                                        'bg-warning text-dark': dorm.occupancyType.includes('Mixed')
                                    }">
                                        <i class="bi bi-people-fill me-1"></i>{{ dorm.occupancyType || 'Unspecified' }}
                                    </span>
                                </p>

                                <!-- Amenities -->
                                <div class="mb-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        <span v-for="amenity in dorm.amenities" :key="amenity.id"
                                            class="badge rounded-pill px-2 py-1"
                                            :class="tenant.preferred_amenities.includes(amenity.id) ? 'bg-success text-white' : 'bg-secondary text-white'">
                                            {{ amenity.aminityName }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Room Features -->
                                <div class="mb-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        <template v-for="room in dorm.rooms" :key="room.roomID">
                                            <span v-for="feature in room.features" :key="feature.id"
                                                class="badge rounded-pill px-2 py-1" :class="tenant.preferred_features && tenant.preferred_features.includes(feature.id)
                                                    ? 'bg-success text-white'
                                                    : 'bg-secondary text-white'">
                                                {{ feature.featureName }}
                                            </span>
                                        </template>
                                    </div>

                                </div>

                                <!-- Rules & Policies -->
                                <div class="mb-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        <span v-for="rule in dorm.rules_and_policy" :key="rule.id"
                                            class="badge rounded-pill px-2 py-1" :class="tenant.preferred_rules && tenant.preferred_rules.includes(rule.id)
                                                ? 'bg-success text-white'
                                                : 'bg-success text-white'">
                                            {{ rule.rulesName }}
                                        </span>
                                    </div>

                                </div>

                                <!-- View Details Button -->
                                <div class="mt-auto">
                                    <button class="btn btn-info rounded-pill w-100" @click="viewDorms(dorm.dormID)">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Mandaue Map -->
            <div class="col-md-6">
                <div class="p-4 rounded-4 shadow-sm map-card">
                    <h2 class="h5 fw-bold mb-3 text-center text-info">Dormitories in Mandaue City</h2>
                    <div id="map-mandaue" class="rounded-3" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Lapu-Lapu Map -->
            <div class="col-md-6">
                <div class="p-4 rounded-4 shadow-sm map-card">
                    <h2 class="h5 fw-bold mb-3 text-center text-info">Dormitories in Lapu-Lapu City</h2>
                    <div id="map" class="rounded-3" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="p-4 rounded shadow-sm text-center mb-4 bg-light border-info">
        <h2 class="h4 fw-bold text-info">Top Rated Dormitories</h2>
        <p class="text-muted">Check out the best dorms in your area</p>
    </div>


    <div class="m-2 py-4">
        <div class="row g-4">
            <!-- Large Left Card -->
            <div class="col-12 col-md-6" v-if="topDorms.length > 0">
                <div class="card h-100 dorm-card text-white border-0 overflow-hidden shadow-lg"
                    style="border-radius: 20px; cursor: pointer; position: relative; height: 400px;">
                    <div :style="{
                        backgroundImage: `url(${topDorms[0].dorm.images?.mainImage || '/default-image.jpg'})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '100%',
                        width: '100%',
                        borderRadius: '20px',
                        filter: 'brightness(0.7)'
                    }"></div>
                    <div style="position: absolute; bottom: 20px; left: 20px; z-index: 10;">
                        <h4 class="fw-bold">{{ topDorms[0].dorm.dormName }}</h4>
                        <p class="mb-1">{{ topDorms[0].dorm.address }}</p>
                        <p class="mb-1">⭐ {{ Number(topDorms[0].avg_rating).toFixed(1) }}</p>
                        <a @click="viewDorms(topDorms[0].dorm.dormID)" class="btn btn-outline-light btn-sm">View
                            Details</a>
                    </div>
                </div>
            </div>


            <!-- Right Column -->
            <div class="col-12 col-md-6">
                <!-- Second Card -->
                <div class="card mb-4 dorm-card text-white border-0 overflow-hidden shadow" v-if="topDorms.length > 1"
                    style="border-radius: 20px; height: 190px; position: relative;">
                    <div :style="{
                        backgroundImage: `url(${topDorms[1].dorm.images?.mainImage || '/default-image.jpg'})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '100%',
                        width: '100%',
                        borderRadius: '20px',
                        filter: 'brightness(0.7)'
                    }"></div>
                    <div style="position: absolute; bottom: 10px; left: 15px; z-index: 10;">
                        <h5 class="fw-bold mb-1">{{ topDorms[1].dorm.dormName }}</h5>
                        <p class="mb-1">{{ topDorms[1].dorm.address }}</p>
                        <p class="mb-0">⭐ {{ Number(topDorms[1].avg_rating).toFixed(1) }}</p>
                        <a @click="viewDorms(topDorms[1].dorm.dormID)"
                            class="btn btn-outline-light btn-sm mt-1">View</a>
                    </div>
                </div>


                <!-- Two Smaller Cards -->
                <div class="row g-3">
                    <div class="col-12 col-md-6" v-for="(dorm, index) in topDorms.slice(2, 4)" :key="dorm.fkdormID">
                        <div class="card dorm-card text-white border-0 overflow-hidden shadow-sm"
                            style="border-radius: 15px; height: 140px; position: relative;">
                            <div :style="{
                                backgroundImage: `url(${dorm.dorm.images?.mainImage || '/default-image.jpg'})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                height: '100%',
                                width: '100%',
                                borderRadius: '15px',
                                filter: 'brightness(0.7)'
                            }"></div>
                            <div style="position: absolute; bottom: 10px; left: 10px; z-index: 10;">
                                <h6 class="fw-bold mb-0">{{ dorm.dorm.dormName }}</h6>
                                <p class="mb-0 text-truncate">{{ dorm.dorm.address }}</p>
                                <p class="mb-0">⭐ {{ Number(dorm.avg_rating).toFixed(1) }}</p>
                                <a @click="viewDorms(dorm.dorm.dormID)"
                                    class="btn btn-outline-light btn-sm mt-1">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fixed Bottom-Right Button -->
    <div v-if="isPersonalized === true">
        <button @click="openPreferences"
            class="btn btn-success btn-lg rounded-pill shadow-lg d-flex align-items-center justify-content-center gap-2 position-fixed"
            style="bottom: 20px; right: 20px; z-index: 1100;">
            <i class="bi bi-gear-fill fs-5"></i>
            Update Your Preferences
        </button>
    </div>


</template>
<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';
import { get } from 'lodash';

export default {
    components: {
        Loader,
        NotificationList,
    },
    data() {
        return {
            rooms: [],
            tenant_id: '',
            tenant: {
                preferred_amenities: [],
                preferred_features: [],
                preferred_rules: []
            },
            notifications: [],
            receiverID: '',
            topDorms: [],
            genderPersonalized: [],
            preferredPrice: null,
            preferredLocation: "",
            showModal: false,
            showWelcome: true,
            showafterWelcome: false,
            showText: [false, false],
            isPersonalized: true,
            afterpersonalized: false,
            aminitiesList: {},
            featuresList: {},
            amenitiesList: {},
            preferredAmenities: [],
            preferredRules: [],
            preferredFeature: [],

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

        viewDorms(dormID) {
            this.tenant_id = window.tenant_id;
            window.location.href = `/room-details/${dormID}/${this.tenant_id}`;
        },
        viewBooking() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/booking/${this.tenant_id}`;

        },
        viewPayment() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/payment/${this.tenant_id}`;

        },
        viewMyrooms() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/myrooms/${this.tenant_id}`;

        },
        viewReservation() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/reservation/${this.tenant_id}`;

        },
        viewnotifications() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/notifications/${this.tenant_id}`;
        },
        async genderPreferencePersonalize() {
            try {

                const response = await axios.get(`/gender-preference-dorms/${this.tenant_id}`);
                this.genderPersonalized = response.data.dorms;
                console.log(this.genderPersonalized);
            } catch (error) {
                console.error(error);
            }
        },
        initMap() {
            this.tenant_id = window.tenant_id;

            const lapuLapu = { lat: 10.3090, lng: 123.9494 };
            const mandaue = { lat: 10.3339, lng: 123.9222 };

            const customStyle = [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ];

            const mapLapu = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: lapuLapu,
                draggable: false,
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                fullscreenControl: false,
                mapTypeId: 'terrain',
                styles: customStyle
            });

            const mapMandaue = new google.maps.Map(document.getElementById("map-mandaue"), {
                zoom: 14,
                center: mandaue,
                draggable: false,
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                fullscreenControl: false,
                mapTypeId: 'terrain',
                styles: customStyle
            });

            const infoWindow = new google.maps.InfoWindow();

            // Fetch Lapu-Lapu Dorms
            axios.get('/tenant/dorms/lapu-lapu')
                .then(response => {
                    response.data.forEach(dorm => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(dorm.latitude),
                                lng: parseFloat(dorm.longitude)
                            },
                            map: mapLapu,
                            title: dorm.dorm_name, // Shown on hover
                            icon: {
                                url: '/images/tenant/allimagesResouces/dormmap.webp',
                                scaledSize: new google.maps.Size(40, 40)
                            }
                        });

                        const content = `
    <div style="
        width: 250px;
        height: 250px;
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        background: #fefefe;
        display: flex;
        flex-direction: column;">
        
        <img src="${dorm.images.mainImage}" alt="Dorm Image"
            style="width: 100%; height: 150px; object-fit: cover; border-bottom: 1px solid #ddd;">
        
        <div class="mb-3" style=" flex: 1;">
            <div style="font-size: 17px; font-weight: 600; color: #2c3e50;">
                🏠 ${dorm.dormName}
            </div>
            <div class="mt-2">
                <a href="/room-details/${dorm.dormID}/${this.tenant_id}" class="btn btn-primary w-100" style="font-size: 14px;">View Details</a>
            </div>
        </div>
    </div>
`;


                        marker.addListener("click", () => {
                            infoWindow.setContent(content);
                            infoWindow.open(mapLapu, marker);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching Lapu-Lapu dorms:', error);
                });

            // Fetch Mandaue Dorms
            axios.get('/tenant/dorms/mandaue')
                .then(response => {
                    response.data.forEach(dorm => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(dorm.latitude),
                                lng: parseFloat(dorm.longitude)
                            },
                            map: mapMandaue,
                            title: dorm.dorm_name, // Shown on hover
                            icon: {
                                url: '/images/tenant/allimagesResouces/dormmap.webp',
                                scaledSize: new google.maps.Size(40, 40)
                            }
                        });

                        const content = `
                      <div style="
        width: 250px;
        height: 250px;
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        background: #fefefe;
        display: flex;
        flex-direction: column;">

        <img src="${dorm.images.mainImage}" alt="Dorm Image"
            style="width: 100%; height: 150px; object-fit: cover; border-bottom: 1px solid #ddd;">
        
        <div class="mb-3" style=" flex: 1;">
            <div style="font-size: 17px; font-weight: 600; color: #2c3e50;">
                🏠 ${dorm.dormName}
            </div>
            <div class="mt-2">
                <a href="/room-details/${dorm.dormID}/${this.tenant_id}" class="btn btn-primary w-100" style="font-size: 14px;">View Details</a>
            </div>
        </div>
    </div>
`;

                        marker.addListener("click", () => {
                            infoWindow.setContent(content);
                            infoWindow.open(mapMandaue, marker);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching Mandaue dorms:', error);
                });
        },
        async fetchTopRatedDorms() {
            try {
                const response = await axios.get('/api/top-rated-dorms');
                // Convert avg_rating to number
                this.topDorms = response.data.map(dorm => ({
                    ...dorm,
                    avg_rating: Number(dorm.avg_rating)
                }));
            } catch (error) {
                console.error(error);
            }
        },
        async getTenant() {
            try {
                const response = await axios.get('/get/preferred-tenants');
                this.isPersonalized = response.data.tenant.isPersonalized;
                this.preferredLocation = response.data.tenant.preferred_location;
                this.preferredPrice = response.data.tenant.preferred_room_price;

            }
            catch (error) {

            }
        },
        async updateSubmitPersonalized() {
            try {
                const payload = {
                    preferredAmenities: this.preferredAmenities,
                    preferredFeature: this.preferredFeature,
                    preferredRules: this.preferredRules,
                    preferredPrice: this.preferredPrice,
                    preferredLocation: this.preferredLocation
                };

                const response = await axios.post('/update/submit-personalized', payload);
                this.showModal = false;
                this.afterpersonalized = true;
                this.showafterWelcome = true;
                this.genderPreferencePersonalize();


                setTimeout(() => {
                    if (this.$refs.loader) {
                        this.$refs.loader.loading = false;
                    }
                    this.showafterWelcome = false;
                    this.afterpersonalized = false;
                }, 3000); // 3 seconds
            } catch (error) {
                console.error('Error saving preferences:', error);
            }
        },

        async welcomeLoader() {
            await this.getTenant();
            this.showWelcome = true;
            // Simulate delay
            setTimeout(() => {
                this.showWelcome = false;
                this.showModal = true;
            }, 3000); // 3 seconds
        },



        startSequence() {
            // Show "Find Your Dorm" after 1 second
            setTimeout(() => {
                this.showText[0] = true;
            }, 1000);

            // Show "Book Now" after 2.5 seconds
            setTimeout(() => {
                this.showText[1] = true;
            }, 1000);
        },
        openPreferences() {
            this.showModal = true;
            this.showWelcome = false;
            this.isPersonalized = false;

        },
        async getPreferences() {
            try {
                const response = await axios.get('/get/room-and-dorm-personalized');

                // Direct assignment (no map)
                this.rulesList = response.data.rulesarray || [];
                this.featuresList = response.data.featureArray || [];
                this.aminitiesList = response.data.amenitiesArray || [];
            } catch (error) {
                console.error('Error fetching preferences:', error);
            }
        },
        toggleAmenity(id) {
            const index = this.preferredAmenities.indexOf(id);
            if (index === -1) {
                this.preferredAmenities.push(id);
            } else {
                this.preferredAmenities.splice(index, 1);
            }
        },
        toggleFeature(id) {
            const index = this.preferredFeature.indexOf(id);
            if (index === -1) {
                this.preferredFeature.push(id);
            } else {
                this.preferredFeature.splice(index, 1);
            }
        },
        toggleRule(id) {
            const index = this.preferredRules.indexOf(id);
            if (index === -1) {
                this.preferredRules.push(id);
            } else {
                this.preferredRules.splice(index, 1);
            }
        },

    },
    mounted() {
        // Load Google Maps script dynamically
        const script = document.createElement("script");
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCbVSKsv35IGFWYg9C96B5swf6UaVj9IGQ&callback=initMap";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        // Attach initMap function globally
        window.initMap = this.initMap;
        this.tenant_id = window.tenant_id;
        this.getPreferences();
        this.subscribeToNotifications();
        this.genderPreferencePersonalize();
        this.fetchTopRatedDorms();
        this.welcomeLoader();
        this.startSequence();


    },
}
</script>
<style scoped>
.welcome-section {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(11, 166, 177, 0.5));
}


.sequential-text span {
    opacity: 0;
    transform: translateY(-10px);
    animation: textFadeIn 0.5s forwards;
}

.sequential-text span:nth-child(1) {
    animation-delay: 0.3s;
}

.sequential-text span:nth-child(2) {
    animation-delay: 0.6s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes textFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* Header animation */
.animated-header {
    opacity: 0;
    transform: translateY(-20px);
    animation: fadeSlideIn 0.6s ease forwards;
}

/* Text sequential fade-in */
.animated-text {
    opacity: 0;
    transform: translateY(10px);
    animation: textFadeIn 0.6s forwards;
}

/* Keyframes */
@keyframes fadeSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes textFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Sequential animation using delay */
.animated-text:nth-child(1) {
    animation-delay: 0.1s;
}

.animated-text:nth-child(2) {
    animation-delay: 0.5s;
}

.animated-text:nth-child(3) {
    animation-delay: 0.8s;
}
</style>
