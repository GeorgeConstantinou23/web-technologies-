// Responsible for cart storage, cart updates, offer codes, and mobile nav
/* ===========================
   GLOBAL VARIABLES
   ========================== */
const cartContainer = document.getElementById("cart-items");
const cartTotalEl = document.getElementById("cart-total");
const applyOfferBtn = document.getElementById("apply-offer");
const offerInput = document.getElementById("offer-code");
const offerMessage = document.getElementById("offer-message");
const clearCartBtn = document.getElementById("clear-cart");
const hamburger = document.querySelector(".hamburger");
const nav = document.querySelector(".main-nav");

let cartItems = [];
let discount = 0;

/* ===========================
   HAMBURGER MENU
   ========================== */
if (hamburger && nav) {
    hamburger.addEventListener("click", () => {
        nav.classList.toggle("active");

        if (nav.classList.contains("active")) {
            hamburger.innerHTML = "&#10006;";
        } else {
            hamburger.innerHTML = "&#9776;";
        }
    });
}

/* ===========================
   COOKIE FUNCTIONS
   ========================== */
function getCartCookie() {
    const cookies = document.cookie.split(";");

    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();

        if (cookie.indexOf("cart=") === 0) {
            return decodeURIComponent(cookie.substring(5));
        }
    }

    return "";
}

function saveCartCookie() {
    const cartText = encodeURIComponent(JSON.stringify(cartItems));
    let cookieText = "cart=" + cartText + "; Max-Age=604800; Path=/; SameSite=Lax";

    if (window.location.protocol === "https:") {
        cookieText += "; Secure";
    }

    document.cookie = cookieText;
}

function clearCartCookie() {
    document.cookie = "cart=; Max-Age=0; Path=/; SameSite=Lax";
}

/* ===========================
   LOAD CART
   ========================== */
function loadCart() {
    const cartCookie = getCartCookie();

    if (cartCookie === "") {
        cartItems = [];
        return;
    }

    try {
        cartItems = JSON.parse(cartCookie);

        if (!Array.isArray(cartItems)) {
            cartItems = [];
        }
    } catch (error) {
        cartItems = [];
    }
}

/* ===========================
   ADD TO CART
   ========================== */
function addToCart(button) {
    if (button.disabled) {
        return;
    }

    let quantity = 1;
    let productId = 0;
    let productName = "Unknown Product";
    let productPrice = 0;
    let productImage = "images/logo_reverse.png";

    if (button.dataset.quantityTarget) {
        const quantityInput = document.getElementById(button.dataset.quantityTarget);

        if (quantityInput) {
            quantity = parseInt(quantityInput.value, 10);
        }
    }

    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
    }

    if (button.dataset.id) {
        productId = parseInt(button.dataset.id, 10);
    }

    if (isNaN(productId)) {
        productId = 0;
    }

    if (button.dataset.name) {
        productName = button.dataset.name;
    }

    if (button.dataset.price) {
        productPrice = parseFloat(button.dataset.price);
    }

    if (button.dataset.image) {
        productImage = button.dataset.image;
    }

    let found = false;

    for (let i = 0; i < cartItems.length; i++) {
        if (cartItems[i].id === productId) {
            cartItems[i].quantity += quantity;
            found = true;
        }
    }

    if (!found) {
        cartItems.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: quantity
        });
    }

    saveCartCookie();
    alert(productName + " added to cart");
}

/* ===========================
   RENDER CART
   ========================== */
function renderCart() {
    if (!cartContainer) {
        return;
    }

    cartContainer.innerHTML = "";

    if (cartItems.length === 0) {
        cartContainer.innerHTML = "<p>Your cart is empty.</p>";

        if (cartTotalEl) {
            cartTotalEl.innerHTML = "";
        }

        return;
    }

    cartItems.forEach((item, index) => {
        const subtotal = item.price * item.quantity;

        const cartDiv = document.createElement("div");
        cartDiv.className = "cart-item";
        cartDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}" width="100">
            <h2>${item.name}</h2>
            <p>Price: &pound;${item.price.toFixed(2)}</p>
            <p>Quantity: <input type="number" class="item-quantity" value="${item.quantity}" min="1"></p>
            <p>Subtotal: &pound;<span class="item-subtotal">${subtotal.toFixed(2)}</span></p>
            <button class="remove-item">Remove</button>
            <hr>
        `;

        cartContainer.appendChild(cartDiv);

        const quantityInput = cartDiv.querySelector(".item-quantity");
        const subtotalEl = cartDiv.querySelector(".item-subtotal");
        const removeBtn = cartDiv.querySelector(".remove-item");

        quantityInput.addEventListener("change", () => {
            let newQty = parseInt(quantityInput.value, 10);

            if (isNaN(newQty) || newQty < 1) {
                newQty = 1;
            }

            quantityInput.value = newQty;
            cartItems[index].quantity = newQty;
            subtotalEl.textContent = (item.price * newQty).toFixed(2);

            saveCartCookie();
            updateTotal();
        });

        removeBtn.addEventListener("click", () => {
            cartItems.splice(index, 1);
            saveCartCookie();
            renderCart();
        });
    });

    updateTotal();
}

/* ===========================
   UPDATE TOTAL
   ========================== */
function updateTotal() {
    if (!cartTotalEl) {
        return;
    }

    let total = 0;

    cartItems.forEach(item => {
        total += item.price * item.quantity;
    });

    if (discount > 0) {
        total = total * (1 - discount);
    }

    cartTotalEl.innerHTML = `<strong>Total: &pound;${total.toFixed(2)}</strong>`;
}

/* ===========================
   APPLY OFFER CODE
   ========================== */
if (applyOfferBtn && offerInput) {
    applyOfferBtn.addEventListener("click", () => {
        const code = offerInput.value.trim().toLowerCase();
        let availableCodes = {};

        if (window.offerCodes) {
            availableCodes = window.offerCodes;
        }

        if (code === "") {
            discount = 0;

            if (offerMessage) {
                offerMessage.textContent = "Please enter a code.";
            }
        } else if (availableCodes[code]) {
            discount = availableCodes[code];

            if (offerMessage) {
                offerMessage.textContent = `Offer applied: ${(discount * 100).toFixed(0)}% off.`;
            }
        } else {
            discount = 0;

            if (offerMessage) {
                offerMessage.textContent = "Invalid code.";
            }
        }

        updateTotal();
    });
}

/* ===========================
   CLEAR CART
   ========================== */
if (clearCartBtn) {
    clearCartBtn.addEventListener("click", () => {
        cartItems = [];
        discount = 0;

        clearCartCookie();

        if (offerInput) {
            offerInput.value = "";
        }

        if (offerMessage) {
            offerMessage.textContent = "";
        }

        renderCart();
    });
}

/* ===========================
   ADD TO CART BUTTONS
   ========================== */
document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", () => {
        addToCart(button);
    });
});

/* ===========================
   INITIAL LOAD
   ========================== */
loadCart();
renderCart();

