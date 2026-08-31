<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Chat History<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/admin/chat.css') ?>?v=<?= ASSET_VERSION ?>">
<meta name="page-config" 
    data-api-get-logs="<?= base_url('api/admin/chat') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="chatApp" v-cloak>
    <div class="card shadow-sm rounded-4 border-0 chat-container" style="height: calc(100vh - 140px); min-height: 500px;">
        <div class="row g-0 h-100">
            <!-- Kolom Kiri: Sesi Chat -->
            <div class="col-md-4 col-lg-3 border-end h-100 d-flex flex-column bg-body-tertiary rounded-start-4" :class="{'d-none d-md-flex': activeNoHp}">
                <div class="p-3 border-bottom bg-body rounded-top-start-4">
                    <h5 class="mb-3 fw-bold text-primary">Chat Sessions</h5>
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-body border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari No HP / Intent..." v-model="search" @input="fetchSessions">
                    </div>
                </div>
                
                <div class="flex-grow-1 overflow-auto session-list">
                    <div v-if="loadingSessions" class="text-center p-4 text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p class="small">Memuat sesi...</p>
                    </div>
                    <div v-else-if="sessions.length === 0" class="text-center p-4 text-muted">
                        <p class="small">Tidak ada sesi ditemukan.</p>
                    </div>
                    <div class="list-group list-group-flush" v-else>
                        <button v-for="session in sessions" :key="session.no_hp" 
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-start py-3"
                                :class="{ 'active bg-primary border-primary': activeNoHp === session.no_hp }"
                                @click="openChat(session.no_hp, session.nama_lengkap, session.foto_profil)">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold text-truncate" style="max-width: 150px;">
                                    <i class="fas fa-user-circle me-1"></i> 
                                    {{ session.nama_lengkap ? session.nama_lengkap : session.no_hp }} 
                                </div>
                                <div class="small" :class="activeNoHp === session.no_hp ? 'text-white-50' : 'text-muted'" v-if="session.nama_lengkap">{{ session.no_hp }}</div>
                            </div>
                            <small :class="activeNoHp === session.no_hp ? 'text-white' : 'text-muted'">{{ formatTime(session.last_activity) }}</small>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Detail Percakapan -->
            <div class="col-md-8 col-lg-9 h-100 d-flex flex-column chat-detail-panel rounded-end-4" :class="{'d-none d-md-flex': !activeNoHp}">
                <template v-if="activeNoHp">
                    <!-- Chat Header -->
                    <div class="p-3 border-bottom bg-body d-flex align-items-center rounded-top-end-4">
                        <!-- Tombol Back untuk Mobile -->
                        <button class="btn btn-sm btn-light d-md-none me-2" @click="activeNoHp = null">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <img :src="getAvatarUrl(activeNamaLengkap, activeFotoProfil)" class="rounded-circle border me-3" width="32" height="32" style="object-fit: cover;">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ activeNamaLengkap ? activeNamaLengkap : activeNoHp }}</h6>
                            <small class="text-muted">{{ activeNoHp }}</small>
                        </div>
                        
                        <!-- Clear Chat Button -->
                        <button class="btn btn-sm btn-outline-danger ms-auto" @click="clearChat" title="Clear Chat History">
                            <i class="fas fa-trash-alt"></i> Clear
                        </button>
                    </div>
                    
                    <!-- Chat Messages -->
                    <div class="flex-grow-1 overflow-auto p-4 chat-messages-bg" ref="chatScroll">
                        <div v-if="loadingChat" class="text-center p-5 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>
                        <template v-else>
                            <div v-for="chat in chatLogs" :key="chat.id" class="d-flex mb-3" :class="chat.sender === 'user' ? 'justify-content-start' : 'justify-content-end'">
                                <div class="chat-bubble shadow-sm" :class="chat.sender === 'user' ? 'bubble-user' : 'bubble-bot'">
                                    <div class="fw-bold small mb-1 opacity-75 d-flex justify-content-between align-items-center">
                                        <span>
                                            <i v-if="chat.sender === 'user'" class="fas fa-user me-1"></i><i v-else class="fas fa-robot text-success me-1"></i> 
                                            {{ chat.sender === 'user' ? (activeNamaLengkap || 'User') : 'Asisten Gracia' }}
                                        </span>
                                        <span v-if="chat.intent" class="ms-3 badge bg-dark bg-opacity-25" style="font-size: 0.65rem;">{{ chat.intent }}</span>
                                    </div>
                                    <!-- Lampiran Media -->
                                    <div v-if="chat.media_url && chat.media_url !== 'null'" class="mb-2">
                                        <div v-if="!chat.mediaError" class="bg-body p-1 rounded border d-inline-block">
                                            <img :src="chat.media_url" @error="chat.mediaError = true" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" alt="Lampiran Media">
                                        </div>
                                        <!-- Fallback Kamuflase (Desain Minimalis) -->
                                        <div v-else class="p-2 px-3 bg-body-tertiary border border-secondary-subtle rounded-3 d-inline-flex align-items-center text-secondary shadow-sm">
                                            <i class="fa-solid fa-image-slash me-2 opacity-75"></i>
                                            <span class="small fw-medium opacity-75">Lampiran Media</span>
                                        </div>
                                    </div>
                                    <div class="chat-text" style="white-space: pre-wrap;">{{ chat.message }}</div>
                                    <div class="text-end small mt-1 opacity-75" style="font-size: 0.7rem;">
                                        {{ formatTime(chat.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <div v-else class="h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="fab fa-whatsapp fa-4x mb-3 opacity-25"></i>
                        <h5>Pilih sesi chat untuk melihat histori</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin/chat.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>



