<?php 
    $namaLengkap = session()->get('nama_lengkap') ?? 'Pengguna';
    $fotoProfil = session()->get('foto_profil');
    if (!empty($fotoProfil)) {
        $avatarUrl = base_url('dokumen/karyawan/' . $fotoProfil);
    } else {
        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($namaLengkap) . "&background=2B3385&color=fff";
    }
    $version = file_exists(ROOTPATH . 'version.txt') ? file_get_contents(ROOTPATH . 'version.txt') : 'v1.0.0';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asisten Gracia AI</title>
    
    <!-- Skrip Tema (dijalankan awal untuk menghindari FOUC) -->
    <script src="<?= base_url('js/theme.js') ?>?v=<?= ASSET_VERSION ?>"></script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Base Styles (Mewarisi font-family system-ui) -->
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>?v=<?= ASSET_VERSION ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/ai-theme.css">
</head>
<body class="ai-body">
    <div id="app" class="d-flex vh-100 overflow-hidden" v-cloak>
        
        <!-- Sidebar: Riwayat Sesi (Desktop) -->
        <div class="sidebar d-none d-md-flex flex-column flex-shrink-0 p-3 bg-body-tertiary border-end border-secondary-subtle" style="width: 280px;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none text-light">
                <i class="fa-solid fa-robot fs-4 me-2 text-primary" style="color: var(--gkr-primary) !important;"></i>
                <span class="fs-5 fw-semibold">Asisten Gracia</span>
            </a>
            <hr class="border-secondary-subtle">
            
            <button class="btn btn-primary mb-3 text-start rounded-3 shadow-sm d-flex align-items-center justify-content-center" @click="createNewChat" style="background-color: var(--gkr-primary); border: none;">
                <i class="fa-solid fa-plus me-2"></i> Percakapan Baru
            </button>
            
            <div class="text-secondary small fw-bold mb-2 ps-2">RIWAYAT SESI</div>
            <ul class="nav nav-pills flex-column mb-auto overflow-auto session-list">
                <li class="nav-item">
                    <a href="#" class="nav-link active rounded-3 d-flex align-items-center py-2 px-3 mb-1">
                        <i class="fa-regular fa-message me-2"></i>
                        <span class="text-truncate">Percakapan Utama (Aktif)</span>
                    </a>
                </li>
            </ul>
            
            <hr class="border-secondary-subtle">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-light" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar rounded-circle overflow-hidden me-2 border border-secondary-subtle shadow-sm" style="width: 32px; height: 32px;">
                        <img src="<?= $avatarUrl ?>" alt="Avatar">
                    </div>
                    <strong><?= esc($namaLengkap) ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="/profile">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Offcanvas Sidebar (Mobile) -->
        <div class="offcanvas offcanvas-start bg-body-tertiary" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel">
            <div class="offcanvas-header border-bottom border-secondary-subtle">
                <h5 class="offcanvas-title text-light d-flex align-items-center" id="sidebarMobileLabel">
                    <i class="fa-solid fa-robot fs-4 me-2" style="color: var(--gkr-primary);"></i> Asisten Gracia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <button class="btn btn-primary mb-3 text-start rounded-3 shadow-sm d-flex align-items-center justify-content-center" @click="createNewChat" data-bs-dismiss="offcanvas" style="background-color: var(--gkr-primary); border: none;">
                    <i class="fa-solid fa-plus me-2"></i> Percakapan Baru
                </button>
                
                <div class="text-secondary small fw-bold mb-2 ps-2">RIWAYAT SESI</div>
                <ul class="nav nav-pills flex-column mb-auto overflow-auto session-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link active rounded-3 d-flex align-items-center py-2 px-3 mb-1" data-bs-dismiss="offcanvas">
                            <i class="fa-regular fa-message me-2"></i>
                            <span class="text-truncate">Percakapan Utama (Aktif)</span>
                        </a>
                    </li>
                </ul>
                
                <hr class="border-secondary-subtle">
                <div class="dropdown mt-auto">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-light" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar rounded-circle overflow-hidden me-2 border border-secondary-subtle shadow-sm" style="width: 32px; height: 32px;">
                            <img src="<?= $avatarUrl ?>" alt="Avatar">
                        </div>
                        <strong><?= esc($namaLengkap) ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                        <li><a class="dropdown-item" href="/profile">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar</a></li>
                    </ul>
                </div>
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
                    <i class="fa-solid fa-robot fs-3 me-2 d-none d-sm-block" style="color: var(--gkr-primary);"></i>
                    <h5 class="mb-0 fw-semibold text-light">Asisten Gracia</h5>
                </div>
                <div>
                    <a href="/" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="fa-solid fa-house me-1"></i> <span class="d-none d-sm-inline">Beranda</span> ERP</a>
                </div>
            </header>

            <!-- Chat History -->
            <div class="chat-history flex-grow-1 overflow-auto p-2 p-md-4" ref="chatContainer">
                <div class="max-w-chat w-100 mx-auto">
                    
                    <!-- Initial Bot Message -->
                    <div class="chat-message bot mb-4 d-flex align-items-start">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-secondary-subtle" style="background-color: var(--gkr-primary) !important;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div class="message-bubble bot-bubble p-3 rounded-4 shadow-sm">
                            <p class="mb-0">Halo! Saya Asisten Gracia. Ada yang bisa saya bantu terkait data ERP atau katalog hari ini?</p>
                        </div>
                    </div>

                    <!-- Dynamic Messages -->
                    <div v-for="msg in messages" :key="msg.id" :class="['chat-message mb-4 d-flex', msg.sender === 'user' ? 'user flex-row-reverse' : 'bot align-items-start']">
                        
                        <!-- Bot Avatar -->
                        <div v-if="msg.sender === 'bot'" class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-secondary-subtle" style="background-color: var(--gkr-primary) !important;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        
                        <!-- User Avatar -->
                        <div v-if="msg.sender === 'user'" class="avatar bg-secondary text-white rounded-circle overflow-hidden d-flex align-items-center justify-content-center ms-3 shadow-sm border border-secondary-subtle">
                            <img :src="'<?= $avatarUrl ?>'" alt="User Avatar">
                        </div>

                        <div :class="['message-bubble p-3 rounded-4 shadow-sm', msg.sender === 'user' ? 'user-bubble' : 'bot-bubble']">
                            <div class="message-content" v-html="renderMarkdown(msg.message)"></div>
                            <div class="message-meta small mt-1" :class="msg.sender === 'user' ? 'text-start' : 'text-end'" style="opacity: 0.7; font-size: 0.75rem;">
                                {{ formatTime(msg.created_at) }}
                                <i v-if="msg.sender === 'user' && msg.source === 'whatsapp'" class="fa-brands fa-whatsapp ms-1" title="Dari WhatsApp"></i>
                                <i v-if="msg.sender === 'user' && msg.source === 'web'" class="fa-solid fa-globe ms-1" title="Dari Web"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div v-if="isLoading" class="chat-message bot mb-4 d-flex align-items-start">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-secondary-subtle" style="background-color: var(--gkr-primary) !important;">
                            <i class="fa-solid fa-robot"></i>
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
                            <button class="btn btn-primary rounded-pill px-4" type="submit" :disabled="!newMessage.trim() || isLoading" style="background-color: var(--gkr-primary); border: none;">
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
                            <span id="themeIcon">dYOT</span> Tema
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
                        <a href="<?= base_url('versi') ?>"><?= esc($version) ?></a>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="/assets/js/ai-app.js"></script>
</body>
</html>
