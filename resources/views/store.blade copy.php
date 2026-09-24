<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UR Cookies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-100 h-screen overflow-hidden text-gray-800" x-data="storeApp()">

    <div class="flex h-screen w-full">
        <!-- ================= LEFT INTERACTIVE ORDERING PANEL ================= -->
        <main class="w-full lg:w-1/2 flex flex-col h-full bg-white border-r border-gray-200">

            <!-- Header[cite: 1] -->
            <header class="p-3 border-b flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="bg-[#b5122b] text-white font-black text-sm px-2.5 py-1 rounded">UR</div>
                    <div>
                        <h1 class="font-bold text-sm">UR Cookies</h1>
                        <p class="text-[11px] text-gray-400">yes , it's UR cookies</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 text-gray-600">
                    <button @click="view = 'cart'" class="relative p-2">
                        <i class="fa fa-shopping-bag"></i>
                        <span x-show="cart.length > 0" x-text="cart.length" class="absolute top-0 right-0 bg-[#b5122b] text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold"></span>
                    </button>
                    <button class="text-xs font-bold">ع</button>
                </div>
            </header>

            <!-- Method Switcher (Delivery / Pickup)[cite: 1] -->
            <div class="p-3 bg-gray-50 border-b space-y-2">
                <div class="flex max-w-xs mx-auto bg-gray-200 p-0.5 rounded-md">
                    <button @click="method = 'delivery'" :class="method === 'delivery' ? 'bg-[#b5122b] text-white font-bold' : 'text-gray-600'" class="flex-1 py-1 text-xs rounded transition">
                        Delivery
                    </button>
                    <button @click="method = 'pickup'" :class="method === 'pickup' ? 'bg-[#b5122b] text-white font-bold' : 'text-gray-600'" class="flex-1 py-1 text-xs rounded transition">
                        Pickup
                    </button>
                </div>
                <div class="flex justify-between items-center text-xs px-2">
                    <div>
                        <span class="text-gray-500" x-text="method === 'delivery' ? 'Deliver to:' : 'Store:'"></span>
                        <strong class="ml-1 text-gray-800" x-text="selectedLocation ? selectedLocation.name : 'Choose location'"></strong>
                    </div>
                    <button @click="view = (method === 'delivery' ? 'location-delivery' : 'location-pickup')" class="text-[#b5122b] font-bold underline">Edit</button>
                </div>
            </div>

            <!-- DYNAMIC STEP VIEWS[cite: 1] -->
            <div class="flex-1 overflow-y-auto custom-scroll p-4">

                <!-- 1. LANDING / PRODUCTS[cite: 1] -->
                <div x-show="view === 'menu'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-sm uppercase text-gray-500">Cookies</h2>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($products as $product)
                            <div class="border rounded-lg p-2.5 flex flex-col justify-between hover:shadow-sm">
                                <img src="https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400" class="w-full h-32 object-cover rounded mb-2">
                                <div>
                                    <h3 class="font-bold text-xs">{{ $product->name }}</h3>
                                    <p class="text-[11px] text-gray-400">{{ $product->description }}</p>
                                </div>
                                <div class="mt-3">
                                    <div class="text-xs font-bold text-[#b5122b] mb-1">
                                        {{ $product->base_price ? number_format($product->base_price, 3) . ' KD' : 'Price on selection' }}
                                    </div>
                                    <button @click="openAddonModal({{ json_encode($product) }})" class="w-full border border-[#b5122b] text-[#b5122b] text-xs font-bold py-1 rounded hover:bg-red-50">
                                        + Add
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. DELIVERY LOCATION ACCORDION[cite: 1] -->
                <div x-show="view === 'location-delivery'" class="space-y-2">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'menu'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Kuwait Locations</h3>
                    </div>
                    @foreach($governorates as $gov)
                        <div class="border rounded text-xs" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full p-2.5 bg-gray-50 flex justify-between font-semibold">
                                <span>{{ $gov->name_en }}</span>
                                <i class="fa fa-chevron-down text-gray-400" :class="open ? 'rotate-180 transform' : ''"></i>
                            </button>
                            <div x-show="open" class="p-2 space-y-1 bg-white">
                                @foreach($gov->areas as $area)
                                    <button @click="selectLocation('{{ $area->name_en }}', {{ $area->delivery_fee }})" class="block w-full text-left py-1 text-gray-600 hover:text-[#b5122b]">
                                        {{ $area->name_en }} ({{ number_format($area->delivery_fee, 3) }} KD)
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. PICKUP STORE SELECTOR[cite: 1] -->
                <div x-show="view === 'location-pickup'" class="space-y-3">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'menu'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Choose a Store</h3>
                    </div>
                    @foreach($stores as $st)
                        <button @click="selectLocation('{{ $st->name }}', 0.000)" class="w-full text-left border p-3 rounded font-bold text-xs hover:border-[#b5122b]">
                            {{ $st->name }} - <span class="font-normal text-gray-400">{{ $st->address }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- 4. PRODUCT ADD-ON CONFIGURATOR[cite: 1] -->
                <div x-show="view === 'addon-modal'" class="space-y-4" x-cloak>
                    <div class="flex items-center">
                        <button @click="view = 'menu'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm" x-text="activeProduct?.name"></h3>
                    </div>

                    <template x-for="group in activeProduct?.addon_groups" :key="group.id">
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-xs mb-2">
                                <span class="font-bold uppercase" x-text="group.name"></span>
                                <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500" x-text="group.is_required ? 'Required' : 'Optional'"></span>
                            </div>
                            <div class="space-y-2">
                                <template x-for="option in group.options" :key="option.id">
                                    <label class="flex justify-between items-center p-2 border rounded cursor-pointer text-xs">
                                        <div class="flex items-center space-x-2">
                                            <input :type="group.type" :name="'grp_' + group.id" :value="option.id" @change="toggleAddon(group, option)" class="text-[#b5122b]">
                                            <span x-text="option.name"></span>
                                        </div>
                                        <span class="font-bold text-[#b5122b]" x-text="'+ ' + parseFloat(option.price).toFixed(3) + ' KD'"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- 5. CART SUMMARY[cite: 1] -->
                <div x-show="view === 'cart'" class="space-y-3">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'menu'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Shopping Cart</h3>
                    </div>
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="border-b pb-2 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-xs" x-text="item.name"></h4>
                                <template x-for="add in item.addons">
                                    <p class="text-[11px] text-gray-400" x-text="add.name + ' (+ ' + parseFloat(add.price).toFixed(3) + ' KD)'"></p>
                                </template>
                                <span class="text-xs font-bold text-[#b5122b]" x-text="item.total_price.toFixed(3) + ' KD'"></span>
                            </div>
                            <button @click="cart.splice(idx, 1)" class="text-red-500 text-xs"><i class="fa fa-trash"></i></button>
                        </div>
                    </template>
                </div>

                <!-- 6. CONTACT / SIGN UP / GUEST[cite: 1] -->
                <div x-show="view === 'auth-select'" class="space-y-4 text-center py-6">
                    <i class="fa fa-id-card text-4xl text-cyan-600"></i>
                    <h3 class="font-bold text-sm">Contact Information</h3>
                    <button @click="view = 'auth-email'" class="w-full bg-[#b5122b] text-white text-xs font-bold py-2.5 rounded">SIGN UP / LOGIN</button>
                    <div><button @click="view = 'checkout-guest'" class="text-xs font-bold underline text-gray-600">Or continue as Guest</button></div>
                </div>

                <!-- 7. EMAIL REGISTRATION & OTP[cite: 1] -->
                <div x-show="view === 'auth-email'" class="space-y-3">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'auth-select'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Register</h3>
                    </div>
                    <div x-show="!otpSent" class="space-y-2">
                        <input type="email" x-model="authForm.email" placeholder="Email *" class="w-full border p-2 text-xs rounded">
                        <input type="password" x-model="authForm.password" placeholder="Password *" class="w-full border p-2 text-xs rounded">
                        <button @click="sendOtp()" class="w-full bg-[#b5122b] text-white text-xs font-bold py-2 rounded">Register & Send Code</button>
                    </div>
                    <div x-show="otpSent" class="space-y-2">
                        <p class="text-xs text-gray-500">Enter the 6 digit code sent to your email:</p>
                        <input type="text" x-model="authForm.code" placeholder="Confirmation code *" maxlength="6" class="w-full border p-2 text-xs rounded">
                        <button @click="verifyOtp()" class="w-full bg-cyan-600 text-white text-xs font-bold py-2 rounded">VERIFY</button>
                    </div>
                </div>

                <!-- 8. GUEST / CONTACT DETAILS[cite: 1] -->
                <div x-show="view === 'checkout-guest'" class="space-y-3">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'auth-select'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Contact Details</h3>
                    </div>
                    <input type="text" x-model="customer.name" placeholder="Name *" class="w-full border p-2 text-xs rounded">
                    <input type="email" x-model="customer.email" placeholder="Email (for your invoice) *" class="w-full border p-2 text-xs rounded">
                    <input type="tel" x-model="customer.phone" placeholder="Phone (+965) *" class="w-full border p-2 text-xs rounded">
                </div>

                <!-- 9. KUWAIT ADDRESS FORM[cite: 1] -->
                <div x-show="view === 'checkout-address'" class="space-y-3">
                    <div class="flex items-center mb-3">
                        <button @click="view = 'checkout-guest'" class="text-xs font-bold mr-2"><i class="fa fa-arrow-left"></i></button>
                        <h3 class="font-bold text-sm">Delivery Address</h3>
                    </div>
                    <div class="flex space-x-2 text-xs">
                        <button @click="address.type = 'Home'" :class="address.type === 'Home' ? 'bg-[#b5122b] text-white' : 'border'" class="flex-1 py-1.5 rounded font-bold">Home</button>
                        <button @click="address.type = 'Apartment'" :class="address.type === 'Apartment' ? 'bg-[#b5122b] text-white' : 'border'" class="flex-1 py-1.5 rounded font-bold">Apartment</button>
                        <button @click="address.type = 'Office'" :class="address.type === 'Office' ? 'bg-[#b5122b] text-white' : 'border'" class="flex-1 py-1.5 rounded font-bold">Office</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" x-model="address.block" placeholder="Block *" class="border p-2 text-xs rounded">
                        <input type="text" x-model="address.street" placeholder="Street *" class="border p-2 text-xs rounded">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" x-model="address.building" placeholder="Building / House *" class="border p-2 text-xs rounded">
                        <input type="text" x-model="address.paci" placeholder="PACI (Optional)" class="border p-2 text-xs rounded">
                    </div>
                </div>

                <!-- 10. FINAL REVIEW & PAYMENT[cite: 1] -->
                <div x-show="view === 'checkout-final'" class="space-y-4">
                    <h3 class="font-bold text-sm">Payment Method</h3>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center space-x-2 border p-2 rounded cursor-pointer">
                            <input type="radio" value="knet" x-model="paymentMethod">
                            <span class="font-bold">Debit Card (KNET)</span>
                        </label>
                        <label class="flex items-center space-x-2 border p-2 rounded cursor-pointer">
                            <input type="radio" value="cash" x-model="paymentMethod">
                            <span class="font-bold">Cash on Delivery</span>
                        </label>
                    </div>
                    <div class="border-t pt-2 text-xs space-y-1">
                        <div class="flex justify-between"><span>Subtotal:</span><span x-text="calculateSubtotal().toFixed(3) + ' KD'"></span></div>
                        <div class="flex justify-between"><span>Delivery:</span><span x-text="deliveryFee.toFixed(3) + ' KD'"></span></div>
                        <div class="flex justify-between font-bold text-sm"><span>Total:</span><span x-text="calculateTotal().toFixed(3) + ' KD'"></span></div>
                    </div>
                </div>

                <!-- 11. ORDER CONFIRMATION SCREEN[cite: 1] -->
                <div x-show="view === 'order-success'" class="text-center py-10 space-y-3" x-cloak>
                    <i class="fa fa-check-circle text-5xl text-green-500"></i>
                    <h2 class="font-black text-base">Order Placed Successfully!</h2>
                    <p class="text-xs text-gray-500">Order Number: <strong x-text="completedOrderNumber"></strong></p>
                    <button @click="location.reload()" class="bg-[#b5122b] text-white px-6 py-2 rounded text-xs font-bold">Back to Store</button>
                </div>

            </div>

            <!-- Sticky Bottom Action Button[cite: 1] -->
            <div class="p-3 border-t bg-white" x-show="view !== 'order-success'">
                <template x-if="view === 'menu' && cart.length > 0">
                    <button @click="view = 'cart'" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded flex justify-between px-3">
                        <span class="bg-[#8f0d21] px-1.5 py-0.5 rounded" x-text="cart.length"></span>
                        <span>Review Order</span>
                        <span x-text="calculateTotal().toFixed(3) + ' KD'"></span>
                    </button>
                </template>
                <template x-if="view === 'addon-modal'">
                    <button @click="addToCartFromModal()" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded">
                        Add to Cart (<span x-text="modalPrice.toFixed(3) + ' KD'"></span>)
                    </button>
                </template>
                <template x-if="view === 'cart'">
                    <button @click="view = 'auth-select'" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded">
                        Go to Checkout (<span x-text="calculateTotal().toFixed(3) + ' KD'"></span>)
                    </button>
                </template>
                <template x-if="view === 'checkout-guest'">
                    <button @click="view = (method === 'delivery' ? 'checkout-address' : 'checkout-final')" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded">
                        Next
                    </button>
                </template>
                <template x-if="view === 'checkout-address'">
                    <button @click="view = 'checkout-final'" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded">
                        Next
                    </button>
                </template>
                <template x-if="view === 'checkout-final'">
                    <button @click="submitOrder()" class="w-full bg-[#b5122b] text-white text-xs font-bold py-3 rounded">
                        Place Order (<span x-text="calculateTotal().toFixed(3) + ' KD'"></span>)
                    </button>
                </template>
            </div>
        </main>

        <!-- ================= RIGHT STATIC BRAND BANNER PANEL[cite: 1] ================= -->
        <aside class="hidden lg:block lg:w-1/2 h-full relative bg-amber-50">
            <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=1200" class="w-full h-full object-cover">
            <div class="absolute bottom-6 right-6">
                <div class="bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white p-3 rounded-2xl shadow">
                    <i class="fab fa-instagram text-2xl"></i>
                </div>
            </div>
        </aside>
    </div>

    <!-- Alpine.js Application Store Logic -->
    <script>
        function storeApp() {
            return {
                view: 'menu',
                method: 'delivery',
                selectedLocation: { name: 'Sharq', fee: 0.950 },
                deliveryFee: 0.950,
                cart: [],
                activeProduct: null,
                selectedAddons: [],
                modalPrice: 0.000,
                authForm: { email: '', password: '', code: '' },
                otpSent: false,
                customer: { name: '', email: '', phone: '' },
                address: { type: 'Home', block: '', street: '', building: '', paci: '' },
                paymentMethod: 'knet',
                completedOrderNumber: '',

                selectLocation(name, fee) {
                    this.selectedLocation = { name, fee };
                    this.deliveryFee = fee;
                    this.view = 'menu';
                },

                openAddonModal(product) {
                    this.activeProduct = product;
                    this.selectedAddons = [];
                    this.modalPrice = parseFloat(product.base_price || 0);
                    this.view = 'addon-modal';
                },

                toggleAddon(group, option) {
                    if (group.type === 'radio') {
                        this.selectedAddons = this.selectedAddons.filter(a => a.group_id !== group.id);
                    }
                    this.selectedAddons.push({
                        group_id: group.id,
                        name: option.name,
                        price: parseFloat(option.price)
                    });
                    const addonsTotal = this.selectedAddons.reduce((sum, a) => sum + a.price, 0);
                    this.modalPrice = parseFloat(this.activeProduct.base_price || 0) + addonsTotal;
                },

                addToCartFromModal() {
                    this.cart.push({
                        product_id: this.activeProduct.id,
                        name: this.activeProduct.name,
                        unit_price: this.modalPrice,
                        quantity: 1,
                        total_price: this.modalPrice,
                        addons: [...this.selectedAddons]
                    });
                    this.view = 'menu';
                },

                calculateSubtotal() {
                    return this.cart.reduce((sum, item) => sum + item.total_price, 0);
                },

                calculateTotal() {
                    return this.calculateSubtotal() + (this.method === 'delivery' ? this.deliveryFee : 0);
                },

                sendOtp() {
                    fetch('/api/auth/send-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.authForm)
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            this.otpSent = true;
                            alert(data.message);
                        }
                    });
                },

                verifyOtp() {
                    fetch('/api/auth/verify-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.authForm)
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            this.customer.email = data.user.email;
                            this.customer.name = data.user.name;
                            this.view = 'checkout-guest';
                        } else {
                            alert(data.message);
                        }
                    });
                },

                submitOrder() {
                    const payload = {
                        order_type: this.method,
                        customer_name: this.customer.name,
                        customer_email: this.customer.email,
                        customer_phone: this.customer.phone,
                        area_name: this.selectedLocation.name,
                        address_type: this.address.type,
                        block: this.address.block,
                        street: this.address.street,
                        building: this.address.building,
                        paci: this.address.paci,
                        subtotal: this.calculateSubtotal(),
                        delivery_fee: this.method === 'delivery' ? this.deliveryFee : 0,
                        total: this.calculateTotal(),
                        payment_method: this.paymentMethod,
                        items: this.cart
                    };

                    fetch('/api/orders/place', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            this.completedOrderNumber = data.order_number;
                            this.cart = [];
                            this.view = 'order-success';
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>
