<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'pos';
render_admin_layout_start($page, 'Terminal POS');
?>

<div class="pos-modern-container">
    <div class="row g-4">
        <!-- Main Service Grid Section -->
        <div class="col-lg-8">
            <!-- Arrivals Feed (New Component) -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-label mb-0">Upcoming Arrivals (Next 7 Days)</h6>
                    <span class="badge bg-primary text-dark rounded-pill" style="font-size: 0.6rem;">{{ $upcomingAppointments->count() }} PENDING SESSIONS</span>
                </div>
                <div class="arrivals-scroll-container">
                    @forelse($upcomingAppointments as $apt)
                    <div class="arrival-card" id="apt-{{ $apt->id }}" onclick="selectAppointment({{ $apt->id }}, '{{ addslashes($apt->client_name) }}', {{ $apt->service_id }}, '{{ addslashes($apt->service->name) }}', {{ $apt->service->price }})">
                        <div class="arrival-time">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d') }} | {{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                        <div class="arrival-name">{{ $apt->client_name }}</div>
                        <div class="arrival-service">{{ $apt->service->name }}</div>
                    </div>
                    @empty
                    <div class="text-center py-4 w-100 opacity-50">
                        <p class="small mb-0">No confirmed appointments in the next 7 days.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Header & Search (Dynamic Glassmorphic) -->
            <div class="glass-search-header animate-slide-down">
                <div class="row align-items-center g-3">
                    <div class="col-md-5">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <input type="text" id="serviceSearch" placeholder="Search services..." onkeyup="filterServices()">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="category-pills" id="catFilter">
                            <?php foreach ($categories as $cat): ?>
                            <button onclick="filterCat('<?= $cat ?>')" class="cat-pill <?= $cat==='All'?'active':'' ?>" data-cat="<?= $cat ?>">
                                <?= $cat ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Service Grid -->
            <div class="row g-4 mt-1" id="servicesGrid">
                <?php foreach ($services_list as $index => $s): ?>
                <div class="col-md-4 service-item animate-fade-in" style="animation-delay: <?= $index * 0.05 ?>s" data-cat="<?= $s->category ?>" data-name="<?= strtolower($s->name) ?>">
                    <div class="modern-service-card" onclick="addToCart(<?= $s->id ?>, '<?= addslashes($s->name) ?>', <?= $s->price ?>)">
                        <div class="card-inner">
                            <div class="card-glow"></div>
                            <div class="category-tag"><?= $s->category ?></div>
                            <h5 class="service-title"><?= $s->name ?></h5>
                            <p class="service-desc"><?= $s->description ?: 'No description provided.' ?></p>
                            <div class="service-footer">
                                <span class="service-price">₱<?= number_format($s->price, 2) ?></span>
                                <div class="add-indicator">+</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar Section (Modern Glass Terminal) -->
        <div class="col-lg-4">
            <div class="pos-sidebar-wrapper sticky-top" style="top: 80px;">
                <div class="modern-terminal-card shadow-lg">
                    <div class="terminal-header">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="fw-bold mb-0">Current Order</h5>
                            <button onclick="clearCart()" class="clear-btn" id="clearBtn" style="display: none;">Clear All</button>
                        </div>
                        <div class="terminal-status-bar"></div>
                        
                        <div class="classification-badge mt-2" id="classificationBadge">
                            <span class="badge bg-secondary text-white" style="font-size: 0.75rem; letter-spacing: 0.5px;">DIRECT SERVICE (WALK-IN)</span>
                        </div>
                        
                        <!-- Walk-in Details (Only for non-scheduled) -->
                        <div id="walkInFields" class="mt-3 p-3 bg-dark bg-opacity-25 rounded-3 border border-secondary border-opacity-25 animate-fade-in">
                            <label class="form-label small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">Assigned Artist</label>
                            <select id="walkInArtist" class="form-select form-select-sm bg-transparent border-secondary text-white mb-2" style="font-size: 0.75rem;">
                                <option value="" class="bg-dark">Select Artist (Optional)</option>
                                @foreach($artists as $artist)
                                <option value="{{ $artist->id }}" class="bg-dark">{{ $artist->name }}</option>
                                @endforeach
                            </select>
                            <label class="form-label small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">Client Name</label>
                            <input type="text" id="walkInClientName" class="form-control form-control-sm bg-transparent border-secondary text-white" style="font-size: 0.75rem;" placeholder="Walk-in Client">
                        </div>
                    </div>

                    <div class="terminal-body">
                        <div id="cartEmpty" class="text-center py-5">
                            <div class="empty-animation mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width: 80px; opacity: 0.1;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                            </div>
                            <h6 class="fw-bold text-muted-foreground opacity-50">Empty Terminal</h6>
                            <p class="small text-muted opacity-30">Add a service to begin checkout.</p>
                        </div>

                        <div id="cartItems" class="modern-terminal-table-wrapper" style="display: none;">
                            <table class="modern-terminal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">SERVICE</th>
                                        <th style="width: 25%; text-align: center;">QTY</th>
                                        <th style="width: 25%; text-align: right;">PRICE</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Checkout Summary (High-Contrast Absolute) -->
                    <div id="cartSummary" class="terminal-footer mt-auto" style="display: none;">
                        <div class="summary-breakdown">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="label">Subtotal</span>
                                <span class="val" id="subtotal">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mb-4 pt-2 border-top">
                                <span class="total-label">TOTAL PHP</span>
                                <span class="total-val animate-pulse" id="total">₱0.00</span>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <button onclick="checkout('cash')" class="btn-checkout cash w-100">
                                    <svg class="me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>CASH</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button onclick="checkout('online')" class="btn-checkout online w-100">
                                    <svg class="me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582" /></svg>
                                    <span>ONLINE</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Transaction Overlay -->
<div id="checkoutSuccess" class="modern-overlay" style="display: none;">
    <div class="premium-success-card animate-zoom-in">
        <div class="success-ring mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" style="width: 50px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
        </div>
        <h2 class="fw-black mb-2">PAID & COMPLETE</h2>
        <p class="text-muted-foreground mb-4">Processed at <span id="successTime" class="fw-black"></span><br>Official receipt has been prepared.</p>
        
        <div class="d-grid gap-3">
            <button onclick="printReceipt()" class="btn-premium-action dark">
                <svg class="me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.89l-2.25 2.25m0 0l2.25 2.25m-2.25-2.25h16.5M2.25 9.75V4.5A2.25 2.25 0 014.5 2.25h15a2.25 2.25 0 012.25 2.25v5.25m-18 3h18" /></svg>
                PRINT RECEIPT
            </button>
            <button onclick="closeSuccess()" class="btn-premium-action gold">NEXT CUSTOMER</button>
        </div>
    </div>
</div>

<!-- Receipt Template -->
<div id="printableReceipt" class="pos-receipt-hidden">
    <!-- Format remains professional as built previously -->
    <div class="receipt-paper">
        <div class="receipt-header">
            <h2 class="fw-black mb-1">PEDRO'S ARTISAN</h2>
            <p class="mb-0 fw-bold">ONE-STOP SHOP & STUDIO</p>
            <div class="receipt-divider"></div>
            <p class="small">208 Tubuan 2 Bypass Road</p>
            <p class="small">Silang, Cavite 04118 | (02) 123-4567</p>
            <p class="small">www.pedrosartisan.com</p>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-meta text-start">
            <div class="d-flex justify-content-between"><span>DATE:</span><span id="receiptDate"></span></div>
            <div class="d-flex justify-content-between"><span>TIME:</span><span id="receiptTime"></span></div>
            <div class="d-flex justify-content-between"><span>TRX ID:</span><span id="receiptTrx"></span></div>
            <div class="d-flex justify-content-between"><span>METHOD:</span><span id="receiptMethod" class="fw-bold"></span></div>
            <div class="d-flex justify-content-between"><span>CASHIER:</span><span>ADM-01</span></div>
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-items-header d-flex justify-content-between fw-bold small">
            <span style="flex: 2; text-align: left;">DESCRIPTION</span>
            <span style="flex: 0.5; text-align: center;">QTY</span>
            <span style="flex: 1; text-align: right;">AMOUNT</span>
        </div>
        <div class="receipt-divider-thin"></div>
        <div id="receiptItemsDisplay"></div> <!-- Note: Changed ID slightly for distinct rendering -->
        <div class="receipt-divider"></div>
        <div class="receipt-summary">
            <div class="receipt-total d-flex justify-content-between">
                <span>TOTAL PHP</span><span id="rectTotal"></span>
            </div>
        </div>
        <div class="receipt-footer">
            <p class="fw-bold">THANK YOU FOR YOUR PATRONAGE!</p>
            <p class="small mt-2">Follow us on IG/FB: @pedrosartisan</p>
            <p class="x-small mt-3">This serves as your official Sales Invoice.</p>
            <div class="receipt-barcode mt-3">|||| | || ||| | ||| | || ||</div>
        </div>
    </div>
</div>

<style>
    /* Arrivals Feed */
    .section-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--muted-foreground); letter-spacing: 1px; }
    .arrivals-scroll-container { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    .arrival-card { 
        min-width: 200px; background: var(--card); border: 1px solid var(--border); border-radius: 15px; padding: 1.25rem; 
        cursor: pointer; transition: 0.3s; position: relative;
    }
    .arrival-card:hover { border-color: var(--primary); transform: translateY(-3px); }
    .arrival-card.active { border-color: var(--primary); background: rgba(212, 175, 55, 0.05); box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1); }
    .arrival-card.active::after { content: 'SELECTED'; position: absolute; top: 12px; right: 12px; font-size: 0.6rem; font-weight: 900; color: var(--primary); }
    .arrival-time { font-size: 0.75rem; color: var(--muted-foreground); margin-bottom: 6px; font-weight: 700; }
    .arrival-name { font-weight: 800; color: var(--foreground); font-size: 1rem; margin-bottom: 4px; }
    .arrival-service { font-size: 0.8rem; color: var(--primary); font-weight: 600; }

    .pos-modern-container { padding: 1rem 0; }

    /* Glass Search Header */
    .glass-search-header {
        background: rgba(18, 18, 18, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        position: sticky;
        top: 20px;
        z-index: 1000;
        box-shadow: 0 4px 30px rgba(0,0,0,0.2);
    }
    .search-input-wrapper { position: relative; }
    .search-icon { position: absolute; left: 15px; top: 12px; width: 18px; color: var(--foreground); opacity: 0.5; }
    .search-input-wrapper input {
        width: 100%; border-radius: 12px; border: 1px solid var(--border); background: var(--input);
        padding: 10px 15px 10px 45px; font-weight: 600; outline: none; transition: 0.3s; color: var(--foreground);
    }
    .search-input-wrapper input:focus { border-color: var(--primary); background: var(--card); }

    /* Category Portfolio */
    .category-pills { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 5px; }
    .category-pills::-webkit-scrollbar { display: none; }
    .cat-pill {
        background: transparent; border: 1px solid var(--border); color: var(--foreground); border-radius: 30px;
        padding: 8px 18px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1px; transition: 0.3s; white-space: nowrap;
    }
    .cat-pill.active, .cat-pill:hover { background: var(--primary); color: #000000; border-color: var(--primary); }

    /* Modern Service Card */
    .modern-service-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 25px; cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;
        overflow: hidden; height: 100%;
    }
    .modern-service-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.4); border-color: var(--primary); }
    .card-inner { padding: 1.5rem; position: relative; z-index: 2; }
    .category-tag { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 0.5rem; }
    .service-title { font-weight: 900; color: var(--foreground); margin-bottom: 0.5rem; letter-spacing: -0.5px; }
    .service-desc { font-size: 0.8rem; color: var(--muted-foreground); margin-bottom: 1.5rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .service-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem; }
    .service-price { font-size: 1.25rem; font-weight: 900; color: var(--primary); }
    .add-indicator { width: 32px; height: 32px; background: var(--input); color: var(--foreground); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700; transition: 0.3s; }
    .modern-service-card:hover .add-indicator { background: var(--primary); color: #000000; transform: rotate(90deg); }

    /* Modern Terminal Sidebar */
    .modern-terminal-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 35px;
        min-height: calc(100vh - 120px); display: flex; flex-direction: column; overflow: hidden;
    }
    .terminal-header { padding: 1.5rem; background: var(--muted); border-bottom: 1px solid var(--border); }
    .terminal-status-bar { height: 4px; background: var(--primary); width: 40%; border-radius: 10px; margin-top: 10px; }
    .clear-btn { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); border-radius: 30px; padding: 4px 12px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    
    .terminal-body { flex-grow: 1; padding: 1.5rem; overflow-y: auto; overflow-x: hidden; }
    
    /* Modern Terminal Table */
    .modern-terminal-table-wrapper { animation: slide-in 0.4s ease forwards; }
    .modern-terminal-table { width: 100%; border-collapse: collapse; }
    .modern-terminal-table th { 
        font-size: 0.8rem; font-weight: 900; text-transform: uppercase; 
        color: var(--muted-foreground); padding-bottom: 12px; border-bottom: 1px solid var(--border);
        text-align: left;
        letter-spacing: 0.5px;
    }
    .modern-terminal-table td { 
        background: transparent; border-bottom: 1px solid var(--border); 
        padding: 15px 4px; vertical-align: middle; color: var(--foreground);
    }
    
    .item-name-cell { font-weight: 600; color: var(--foreground); font-size: 0.95rem; line-height: 1.3; word-break: break-word; }
    .price-cell { font-weight: 700; color: var(--foreground); font-size: 0.95rem; text-align: right; }
    .qty-cell-inner { display: flex; align-items: center; justify-content: center; gap: 10px; }
    .qty-btn-circle { 
        width: 28px; height: 28px; border: 1px solid var(--border); background: var(--input); 
        color: var(--foreground); border-radius: 50%; font-weight: 700; font-size: 1rem;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .qty-btn-circle:hover { background: var(--primary); color: #000000; border-color: var(--primary); }
    .qty-val-box { font-weight: 700; min-width: 24px; text-align: center; font-size: 1rem; color: var(--primary); }

    .terminal-footer { padding: 1.75rem; background: var(--card); border-top: 1px solid var(--border); }
    .label { font-weight: 600; color: var(--muted-foreground); font-size: 0.9rem; }
    .val { font-weight: 700; color: var(--foreground); font-size: 1rem; }
    .total-label { font-weight: 800; font-size: 1.25rem; color: var(--foreground); letter-spacing: -0.5px; }
    .total-val { font-weight: 900; font-size: 2rem; color: var(--primary); letter-spacing: -1px; }

    .btn-checkout {
        background: var(--primary); color: #000000; border: none; border-radius: 20px;
        padding: 1.15rem; font-weight: 800; font-size: 0.95rem; transition: 0.3s;
        display: flex; justify-content: center; align-items: center; text-transform: uppercase;
        letter-spacing: 0.5px; cursor: pointer; min-height: 56px;
    }
    .btn-checkout.cash { background: var(--primary); }
    .btn-checkout.online { background: #3b82f6; color: #ffffff; }
    .btn-checkout:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

    /* Animations */
    @keyframes fade-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slide-in { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slide-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes zoom-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .animate-fade-in { animation: fade-in 0.5s ease forwards; }
    .animate-slide-down { animation: slide-down 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; }
    .animate-zoom-in { animation: zoom-in 0.4s ease forwards; }
    
    /* Success Modal */
    .modern-overlay { 
        position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 2000;
        display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);
    }
    .premium-success-card { background: var(--card); border: 1px solid var(--border); border-radius: 40px; padding: 4rem; text-align: center; max-width: 500px; width: 90%; }
    .success-ring { width: 100px; height: 100px; border: 4px solid var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--primary); }
    
    .btn-premium-action { border-radius: 20px; padding: 1rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; display: flex; align-items: center; justify-content: center; border: none; }
    .btn-premium-action.dark { background: var(--input); color: var(--foreground); border: 1px solid var(--border); }
    .btn-premium-action.gold { background: var(--primary); color: #000000; }
    .btn-premium-action:hover { transform: scale(1.05); }

    /* Print Logic (Fixed from previous bug) */
    .pos-receipt-hidden { display: none; }
    @media print {
        body * { visibility: hidden !important; }
        #printableReceipt, #printableReceipt * { visibility: visible !important; }
        #printableReceipt { 
            display: block !important; position: absolute !important; left: 0 !important; top: 0 !important; 
            width: 80mm !important; font-family: 'Inconsolata', 'Courier New', Courier, monospace !important;
            background: white !important; color: black !important; padding: 5mm;
        }
        .btn, .modern-overlay, .pos-modern-container { display: none !important; }
    }
</style>

<script>
let cart = [];
let currentCat = 'All';

let selectedAppointmentId = null;

function filterCat(cat) {
    currentCat = cat;
    document.querySelectorAll('#catFilter button').forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
    applyFilters();
}

function filterServices() { applyFilters(); }

function applyFilters() {
    let q = document.getElementById('serviceSearch').value.toLowerCase();
    document.querySelectorAll('.service-item').forEach(el => {
        let matchCat = currentCat === 'All' || el.dataset.cat === currentCat;
        let matchSearch = el.dataset.name.includes(q);
        el.style.display = (matchCat && matchSearch) ? '' : 'none';
        if(matchCat && matchSearch) { el.classList.remove('animate-fade-in'); void el.offsetWidth; el.classList.add('animate-fade-in'); }
    });
}

function selectAppointment(id, client, serviceId, serviceName, price) {
    // Highlight
    document.querySelectorAll('.arrival-card').forEach(c => c.classList.remove('active'));
    document.getElementById('apt-' + id).classList.add('active');
    
    // Set classification
    selectedAppointmentId = id;
    const badge = document.getElementById('classificationBadge');
    badge.innerHTML = `<span class="badge bg-primary text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">SCHEDULED SESSION: ${client.toUpperCase()}</span>`;
    
    // Hide walk-in fields
    document.getElementById('walkInFields').style.display = 'none';
    
    // Clear and add service
    cart = [];
    addToCart(serviceId, serviceName, price);
}

function addToCart(id, name, price) {
    let item = cart.find(i => i.id === id);
    if (item) item.qty++; else cart.push({ id, name, price, qty: 1 });
    renderCart();
}

function changeQty(id, delta) {
    let item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
    renderCart();
}

function renderCart() {
    const empty = document.getElementById('cartEmpty');
    const wrapper = document.getElementById('cartItems');
    const body = document.getElementById('cartTableBody');
    const summary = document.getElementById('cartSummary');
    const clearBtn = document.getElementById('clearBtn');
    
    if (cart.length === 0) {
        empty.style.display = 'block'; wrapper.style.display = 'none'; summary.style.display = 'none'; clearBtn.style.display = 'none'; return;
    }
    
    empty.style.display = 'none'; wrapper.style.display = 'block'; summary.style.display = 'block'; clearBtn.style.display = 'block';
    
    body.innerHTML = cart.map(i => `
        <tr>
            <td class="item-name-cell">${i.name}</td>
            <td style="text-align: center;">
                <div class="qty-cell-inner">
                    <button onclick="changeQty(${i.id}, -1)" class="qty-btn-circle">−</button>
                    <span class="qty-val-box">${i.qty}</span>
                    <button onclick="changeQty(${i.id}, 1)" class="qty-btn-circle">+</button>
                </div>
            </td>
            <td class="price-cell">₱${(i.price * i.qty).toLocaleString()}</td>
        </tr>
    `).join('');

    let sub = cart.reduce((s, i) => s + (i.price * i.qty), 0);
    document.getElementById('subtotal').innerText = '₱' + sub.toLocaleString();
    document.getElementById('total').innerText = '₱' + sub.toLocaleString();
}

function clearCart() { 
    cart = []; 
    renderCart(); 
    selectedAppointmentId = null;
    document.getElementById('walkInFields').style.display = 'block';
    document.getElementById('classificationBadge').innerHTML = `<span class="badge bg-secondary text-white" style="font-size: 0.75rem; letter-spacing: 0.5px;">DIRECT SERVICE (WALK-IN)</span>`;
    document.querySelectorAll('.arrival-card').forEach(c => c.classList.remove('active'));
}

function checkout(method) { 
    if(cart.length === 0) {
        alert('Terminal Error: Please select an appointment or add a service before completing the transaction.');
        return;
    }
    
    // Disable buttons to prevent double click & show loading
    const btns = document.querySelectorAll('.btn-checkout');
    btns.forEach(b => {
        b.disabled = true;
        b.dataset.original = b.innerHTML;
        b.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESSING...';
    });

    let total = cart.reduce((s, i) => s + (i.price * i.qty), 0);
    const trxId = 'TRX-' + Math.floor(100000 + Math.random() * 900000);

    // Send to backend
    fetch('{{ route("admin.pos.checkout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            amount: total,
            method: method,
            transaction_id: trxId,
            appointment_id: selectedAppointmentId,
            artist_id: document.getElementById('walkInArtist').value,
            client_name: document.getElementById('walkInClientName').value,
            items: cart
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('successTime').innerText = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            document.getElementById('checkoutSuccess').style.display = 'flex'; 
            prepareReceipt(trxId, method);
        } else {
            alert('Checkout Error: ' + (data.message || 'Transaction failed on server.'));
            btns.forEach(b => {
                b.disabled = false;
                b.innerHTML = b.dataset.original;
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Connectivity Error: Failed to reach the server. Check your connection.');
        btns.forEach(b => {
            b.disabled = false;
            b.innerHTML = b.dataset.original;
        });
    });
}

function prepareReceipt(trxId, method) {
    const now = new Date();
    document.getElementById('receiptTrx').innerText = trxId;
    document.getElementById('receiptMethod').innerText = method.toUpperCase();
    document.getElementById('receiptDate').innerText = now.toLocaleDateString();
    document.getElementById('receiptTime').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    const items = cart.map(i => `
        <div class="receipt-item py-1">
            <span style="flex: 2; text-align: left;">${i.name}</span>
            <span style="flex: 0.5; text-align: center;">${i.qty}</span>
            <span style="flex: 1; text-align: right;">₱${(i.price * i.qty).toLocaleString()}</span>
        </div>
    `).join('');
    
    document.getElementById('receiptItemsDisplay').innerHTML = items;
    
    let total = cart.reduce((s, i) => s + (i.price * i.qty), 0);
    document.getElementById('rectTotal').innerText = '₱' + total.toLocaleString();
}

function printReceipt() { window.print(); }
function closeSuccess() { 
    document.getElementById('checkoutSuccess').style.display = 'none'; 
    clearCart(); 
    selectedAppointmentId = null;
    document.getElementById('classificationBadge').innerHTML = `<span class="badge bg-secondary text-white" style="font-size: 0.75rem; letter-spacing: 0.5px;">DIRECT SERVICE (WALK-IN)</span>`;
    document.querySelectorAll('.arrival-card').forEach(c => c.classList.remove('active'));
}
</script>

<?php render_admin_layout_end(); ?>
