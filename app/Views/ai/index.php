<?php 
    $namaLengkap = session()->get('nama_lengkap') ?? 'Pengguna';
    $fotoProfil = session()->get('foto_profil');
    if (!empty($fotoProfil)) {
        $avatarUrl = base_url('dokumen/karyawan/' . $fotoProfil);
    } else {
        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($namaLengkap) . "&background=2B3385&color=fff";
    }
    $version = file_exists(ROOTPATH . 'version.txt') ? file_get_contents(ROOTPATH . 'version.txt') : 'v1.0.0';

    $finalUrlLogo = $urlLogo ?? base_url('Gracia_logo.png');
    $finalAltLogo = $altLogo ?? 'PT. Gracia Kreasi Rotan';

    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $dateDesktopStr = $days[date('w')] . ', ' . date('d/m/Y');
    $dateMobileStr = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asisten Gracia</title>
    
    <!-- Skrip Tema (dijalankan awal untuk menghindari FOUC) -->
    <script src="<?= base_url('js/theme.js') ?>?v=<?= ASSET_VERSION ?>"></script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css"/>
    <!-- PrismJS CSS untuk Syntax Highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

    <!-- Base Styles (Mewarisi font-family system-ui) -->
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>?v=<?= ASSET_VERSION ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/ai-theme.css?v=<?= ASSET_VERSION ?>">
</head>
<body class="ai-body">
    <div id="app" class="d-flex vh-100 overflow-hidden" v-cloak>
        
        <!-- Sidebar: Riwayat Sesi (Desktop) -->
        <div id="sidebarDesktop" class="sidebar d-none d-md-flex flex-column flex-shrink-0 p-3 bg-body-tertiary border-end border-secondary-subtle" style="width: 280px;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none text-light">
                <span class="fs-5 fw-semibold">Asisten Gracia</span>
            </a>
            <hr class="border-secondary-subtle">
            
            <button class="btn btn-primary mb-3 text-start rounded-3 shadow-sm d-flex align-items-center justify-content-center" @click="createNewChat" style="background-color: var(--gkr-primary); color: var(--gkr-primary-text) !important; border: none;">
                <i class="fa-solid fa-plus me-2"></i> Percakapan Baru
            </button>
            
            <div class="text-secondary small fw-bold mb-2 ps-2">RIWAYAT SESI</div>
            <ul class="nav nav-pills flex-column mb-auto overflow-auto session-list">
                <li class="nav-item">
                    <a href="#" class="nav-link rounded-3 d-flex align-items-center py-2 px-3 mb-1"
                       :class="{'active': activeSessionId === 'main'}"
                       @click.prevent="selectSession('main')">
                        <i class="fa-brands fa-whatsapp me-2"></i>
                        <span class="text-truncate">Chat Whatsapp</span>
                    </a>
                </li>
                <li class="nav-item" v-for="sesi in sessions" :key="sesi.id">
                    <a href="#" class="nav-link rounded-3 d-flex align-items-center justify-content-between py-2 px-3 mb-1"
                       :class="{'active': activeSessionId === sesi.id}"
                       @click.prevent="selectSession(sesi.id)">
                        <div class="d-flex align-items-center overflow-hidden">
                            <i class="fa-regular fa-message me-2"></i>
                            <span class="text-truncate" style="max-width: 150px;" :title="sesi.title">{{ sesi.title }}</span>
                        </div>
                        <button class="btn btn-sm btn-link text-danger p-0 m-0" @click.prevent.stop="deleteSession(sesi.id)">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </a>
                </li>
            </ul>
            

        </div>
        
        <!-- Offcanvas Sidebar (Mobile) -->
        <div class="offcanvas offcanvas-start bg-body-tertiary" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel">
            <div class="offcanvas-header border-bottom border-secondary-subtle">
                <h5 class="offcanvas-title text-light d-flex align-items-center" id="sidebarMobileLabel">
                    Asisten Gracia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <button class="btn btn-primary mb-3 text-start rounded-3 shadow-sm d-flex align-items-center justify-content-center" @click="createNewChat" data-bs-dismiss="offcanvas" style="background-color: var(--gkr-primary); color: var(--gkr-primary-text) !important; border: none;">
                    <i class="fa-solid fa-plus me-2"></i> Percakapan Baru
                </button>
                
                <div class="text-secondary small fw-bold mb-2 ps-2">RIWAYAT SESI</div>
                <ul class="nav nav-pills flex-column mb-auto overflow-auto session-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link rounded-3 d-flex align-items-center py-2 px-3 mb-1" data-bs-dismiss="offcanvas"
                           :class="{'active': activeSessionId === 'main'}"
                           @click.prevent="selectSession('main')">
                            <i class="fa-brands fa-whatsapp me-2"></i>
                            <span class="text-truncate">Chat Whatsapp</span>
                        </a>
                    </li>
                    <li class="nav-item" v-for="sesi in sessions" :key="sesi.id">
                        <a href="#" class="nav-link rounded-3 d-flex align-items-center justify-content-between py-2 px-3 mb-1" data-bs-dismiss="offcanvas"
                           :class="{'active': activeSessionId === sesi.id}"
                           @click.prevent="selectSession(sesi.id)">
                            <div class="d-flex align-items-center overflow-hidden">
                                <i class="fa-regular fa-message me-2"></i>
                                <span class="text-truncate" style="max-width: 150px;" :title="sesi.title">{{ sesi.title }}</span>
                            </div>
                            <button class="btn btn-sm btn-link text-danger p-0 m-0" @click.prevent.stop="deleteSession(sesi.id)">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </a>
                    </li>
                </ul>
                

            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main flex-grow-1 d-flex flex-column position-relative">
            
            <!-- Top Navbar -->
            <header class="p-3 border-bottom border-secondary-subtle d-flex justify-content-between align-items-center bg-body shadow-sm z-1">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary d-md-none me-3 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
                        <i class="fa-solid fa-bars fs-4"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-none d-md-block me-3 border-0" type="button" onclick="document.getElementById('sidebarDesktop').classList.toggle('d-md-flex')">
                        <i class="fa-solid fa-bars fs-4"></i>
                    </button>
                    <h5 class="mb-0 fw-semibold text-light d-flex align-items-center">
                        Asisten
                        <img src="<?= esc($finalUrlLogo) ?>" alt="<?= esc($finalAltLogo) ?>" title="<?= esc($finalAltLogo) ?>" class="ms-2" style="height: 18px; object-fit: contain;" onerror="this.onerror=null; this.src='<?= base_url('Gracia_logo.png') ?>';">
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="/" class="text-decoration-none text-light fw-medium px-2">
                        <span>Beranda</span>
                    </a>

                    <!-- Kalender -->
                    <div class="dropdown" id="calendarDropdownWrap">
                        <a href="#" id="calendarDropdownToggle" class="text-light small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color=''">
                            <span class="d-none d-sm-inline"><?= $dateDesktopStr ?></span>
                            <span class="d-sm-none"><?= $dateMobileStr ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2 rounded-4 " style="width: 320px; z-index: 1060 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <button type="button" id="prevMonthBtn" class="btn btn-sm btn-link text-decoration-none text-body p-0 px-2"><i class="fas fa-chevron-left"></i></button>
                                <div class="text-center fw-bold" style="color: var(--gkr-primary); font-size: 0.95rem;" id="calendarMonthYearLabel"></div>
                                <button type="button" id="nextMonthBtn" class="btn btn-sm btn-link text-decoration-none text-body p-0 px-2"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <table class="table table-sm table-borderless text-center mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th class="text-muted fw-bold" style="font-size: 0.8rem;">W</th>
                                        <th class="fw-medium">S</th>
                                        <th class="fw-medium">S</th>
                                        <th class="fw-medium">R</th>
                                        <th class="fw-medium">K</th>
                                        <th class="fw-medium">J</th>
                                        <th class="text-danger fw-medium">S</th>
                                        <th class="text-danger fw-medium">M</th>
                                    </tr>
                                </thead>
                                <tbody id="calendarBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Profil Avatar -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-light" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $avatarUrl ?>" alt="Avatar" width="32" height="32" class="rounded-circle border" style="object-fit: cover;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2 text-muted"></i>Profil</a></li>
                            <?php if (session()->get('id_user') == 1): ?>
                                <li><a class="dropdown-item" href="<?= base_url('admin') ?>"><i class="fas fa-user-shield text-muted me-2"></i>Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Chat History -->
            <div class="chat-history flex-grow-1 overflow-auto p-2 p-md-4" ref="chatContainer">
                <div class="max-w-chat w-100 mx-auto">
                    
                    <!-- Initial Bot Message -->
                    <div class="chat-message bot mb-4 d-flex align-items-start">
                        <div class="avatar bot-avatar-circle rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                            <span class="bot-icon-avatar"></span>
                        </div>
                        <div class="message-bubble bot-bubble p-3 rounded-4 shadow-sm">
                            <p class="mb-0">Halo! Saya Asisten Gracia. Ada yang bisa saya bantu terkait data ERP atau katalog hari ini?</p>
                        </div>
                    </div>

                    <!-- Dynamic Messages -->
                    <div v-for="msg in messages" :key="msg.id" :class="['chat-message mb-4 d-flex', msg.sender === 'user' ? 'user flex-row-reverse' : 'bot align-items-start']">
                        
                        <!-- Bot Avatar -->
                        <div v-if="msg.sender === 'bot'" class="avatar bot-avatar-circle rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                            <span class="bot-icon-avatar"></span>
                        </div>
                        
                        <!-- User Avatar -->
                        <div v-if="msg.sender === 'user'" class="avatar bg-secondary text-white rounded-circle overflow-hidden d-flex align-items-center justify-content-center ms-3 shadow-sm border border-secondary-subtle">
                            <img :src="'<?= $avatarUrl ?>'" alt="User Avatar">
                        </div>

                        <div :class="['message-bubble p-3 rounded-4 shadow-sm', msg.sender === 'user' ? 'user-bubble' : 'bot-bubble']">
                            <div class="message-content">
                                <!-- Lampiran Media (Gambar) dengan Fancybox -->
                                <div v-if="msg.media_url" class="mb-2">
                                    <a :href="msg.media_url" data-fancybox="gallery" :data-caption="msg.message">
                                        <img :src="msg.media_url" class="img-fluid rounded" alt="Lampiran Media" style="max-height: 250px; object-fit: cover;">
                                    </a>
                                </div>
                                <!-- Isi Pesan Teks (Markdown) -->
                                <div v-html="renderMarkdown(msg.message)"></div>
                            </div>
                            <div class="message-meta small mt-1" :class="msg.sender === 'user' ? 'text-start' : 'text-end'" style="opacity: 0.7; font-size: 0.75rem;">
                                {{ formatTime(msg.created_at) }}
                                <i v-if="msg.sender === 'user' && msg.source === 'whatsapp'" class="fa-brands fa-whatsapp ms-1" title="Dari WhatsApp"></i>
                                <i v-if="msg.sender === 'user' && msg.source === 'web'" class="fa-solid fa-globe ms-1" title="Dari Web"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div v-if="isLoading" class="chat-message bot mb-4 d-flex align-items-start">
                        <div class="avatar bot-avatar-circle rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                            <span class="bot-icon-avatar"></span>
                        </div>
                        <div class="message-bubble bot-bubble p-3 rounded-4 shadow-sm d-flex align-items-center">
                            <div class="typing-indicator">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Chat Input Form -->
            <div class="chat-input-area p-2 p-md-3 bg-body border-top border-secondary-subtle z-1 shadow-sm">
                <div class="container-fluid max-w-chat px-0">
                    <form @submit.prevent="sendMessage" class="position-relative">
                        <div class="input-group input-group-lg shadow-sm rounded-pill p-1 border border-secondary-subtle">
                            <input type="text" v-model="newMessage" class="form-control border-0 bg-transparent text-light px-4" 
                                placeholder="Ketik pesan atau perintah ERP/Katalog di sini..." 
                                :disabled="isLoading"
                                autocomplete="off"
                                style="box-shadow: none;">
                            <button class="btn btn-primary rounded-pill px-4" type="submit" :disabled="!newMessage.trim() || isLoading" style="background-color: var(--gkr-primary); color: var(--gkr-primary-text) !important; border: none;">
                                <i class="fa-solid fa-paper-plane" v-if="!isLoading"></i>
                                <i class="fa-solid fa-circle-notch fa-spin" v-else></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Mini Footer (Toggle Tema & Versi) -->
            <footer class="ai-mini-footer py-2 w-100 z-1">
                <div class="d-flex justify-content-between align-items-center px-4 max-w-chat mx-auto">
                    <!-- Kiri: Toggle Tema -->
                    <div class="flex-grow-1 text-start">
                        <button id="themeToggleBtn" class="btn btn-sm btn-link p-0 border-0 text-decoration-none" title="Ubah Mode Tema">
                            <span id="themeIcon">dYOT</span>
                        </button>
                    </div>
                    <!-- Tengah: Hak Cipta -->
                    <div class="text-center d-none d-sm-block">
                        <span>Dikembangkan oleh </span>
                        <a href="https://rnd.gkr.my.id" target="_blank" style="color: var(--gkr-primary); font-weight: 500;">RND</a> 
                        &copy; <?= date('Y') ?>
                    </div>
                    <!-- Kanan: Versi -->
                    <div class="flex-grow-1 text-end">
                        <a href="<?= base_url('versi') ?>">v<?= ASSET_VERSION ?></a>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/calendar.js') ?>?v=<?= ASSET_VERSION ?? time() ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <!-- Axios untuk HTTP requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/assets/js/ai-app.js?v=<?= ASSET_VERSION ?? time() ?>"></script>
</body>
</html>
