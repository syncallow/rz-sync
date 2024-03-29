<template>
    <Head title="Shop" />
    <MainLayout>
        <div class="cart-section">
            <h1 class="page-title">Cart</h1>
            <div class="main-content">
                <div class="cart-items">
                    <div class="cart-list">
                        <div v-for="(product, index) in products" :key="product.id" class="cart-card">
                            <div class="card-card__image">
                                <img :src="product.image" :alt="product.title">
                            </div>
                            <div class="card-card__body">
                                <h3>{{ product.title }} <span class="price">{{ product.price }}</span> <span class="currency">SA</span></h3>
                                <p>{{ product.description }}</p>
                                <p>
                                    <span class="btn minus" @click.prevent="update(product, carts[itemId(product.id)].quantity - 1)" :class="carts[itemId(product.id)].quantity > 1 ? '' : 'd-none'">-</span>
                                    <input class="quantity" @change.prevent="update(product, carts[itemId(product.id)].quantity)" type="number" min="1" v-model="carts[itemId(product.id)].quantity">
                                    <span class="btn plus" @click.prevent="update(product, carts[itemId(product.id)].quantity + 1)">+</span>
                                </p>
                            </div>
                            <div class="remove-product" :class="{'processing': isProcessing}" @click.prevent="remove(product, index)"></div>
                        </div>
                    </div>
                </div>

                <div class="checkout">
                    <p v-if="page.props.flash.error" class="text-red-600">{{ page.props.flash.error }}</p>
                    <h2>Total: <span class="price">{{ total }}</span> <span class="currency">SA</span></h2>
                    <button @click.prevent="checkout()" :disabled="isProcessing">Checkout</button>
                </div>
<!--                <div class="character-loading">-->
<!--                    <div class="loader violet">-->
<!--                        <div class="inner one"></div>-->
<!--                        <div class="inner two"></div>-->
<!--                        <div class="inner three"></div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import {computed, onBeforeUnmount, onMounted, ref, toRefs} from "vue";
import {Head, Link, router, usePage} from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue';
import Products from '@/Pages/Shop/Partials/Products.vue';
import Swal from "sweetalert2";

const page = usePage();
const authUser = page.props.auth.user
const isProcessing = ref(false);

/**
 * Set cart variables
 * @carts current cart items
 * @products current cart items info
 * @total order's total price
 * 'itemId' finds cart items equal with product to get info
 */
const carts = computed(() => page.props.cart.data.items);
const products = computed(() => page.props.cart.data.products);
const total = computed(() => page.props.cart.data.total);
const itemId = (id) => carts.value.findIndex((item) => item.product_id == id);
/**
 * @function update
 * Updates quantity in database
 * @function delete
 * Deletes product from cart
 * @param product
 * @param quantity
 */
const update = async (product, quantity) => {
    try {
        isProcessing.value = true;
        const response = await axios.patch(route('shop.cart.update', product), {
            quantity
        });

        if (response.data.success) {
            router.reload({ only: ['cart'] })
            isProcessing.value = false;
        } else {
            console.log(response.data)
        }
    } catch (error) {
        console.error(error)
    }

    // router.patch(route('shop.cart.update', product),
    //     {
    //         quantity
    //     }, {
    //         preserveScroll:true,
    //         onSuccess: () => {
    //             isProcessing.value = false;
    //         }
    //     }
    // );
}
const remove = async (product, index) => {
    if (isProcessing.value) return console.log('wait')

        isProcessing.value = true;
        const response = await axios.delete(route('shop.cart.delete', product))
            if (response.data.success) {
                products.value.splice(index, 1);
                router.reload({ only: ['cart'] })
            }
            if (products.value.length < 1) return router.visit(route('index'))
        isProcessing.value = false;
}

/**
 * Checkout function
 */

const checkout = () => {
    isProcessing.value = true;
    router.visit(route('shop.cart.checkout'),{
       method: 'post',
       data: {
           carts: page.props.cart.data.items,
           products: page.props.cart.data.products,
           total: page.props.cart.data.total
       }
    });
}
</script>

<style scoped>
h3 {
    font-size: 17px;
}
.cart-list {
    display: flex;
    flex-direction: column;
    row-gap: 20px;
}
.cart-card {
    width: 100%;
    display: flex;
    align-items: center;
    column-gap: 4rem;
    border: 1px solid #0f0f0f;
    padding: 20px;
    position: relative;
}
.remove-product {
    position: absolute;
    width: 24px;
    height: 24px;
    top: 0;
    right: 0;
    background: #de0165;
    cursor: pointer;
}

.remove-product.processing {
    opacity: .5;
}

.remove-product::before {
    content: '';
    position: absolute;
    width: 100%;
    top: 38%;
    height: 3px;
    background: #ffffff;
    border-radius: 5px;
    transform: rotate(45deg);

}

.remove-product::after {
    content: '';
    position: absolute;
    width: 100%;
    top: 38%;
    height: 3px;
    background: #ffffff;
    border-radius: 5px;
    transform: rotate(-45deg);

}
.price {
    padding-left: 20px;
    font-size: 18px;
    font-weight: 800;
}
.currency {
    font-size: 18px;
    font-weight: 500;
    color: #d0016a;
}
h2 {
    margin-bottom: 0;
}
h2 .price {
    padding-left: 5px;
}
.quantity {
    width: 20%;
    padding: 5px;
    margin-inline: 10px;
}

.quantity::-webkit-outer-spin-button,
.quantity::-webkit-inner-spin-button {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.btn {
    color: #ffffff;
    padding: 3px 6px;
    border-radius: 5px;
    cursor: pointer;
}
.plus {
    background: #1CA32B;
}
.minus {
    background: #c90364;
}

.checkout {
    margin-top: 30px;
    display: flex;
    align-items: center;
    justify-content:flex-end;
    column-gap: 30px;
}
</style>
