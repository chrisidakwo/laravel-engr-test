<template>
    <div class="p-6 max-w-2xl mx-auto bg-white rounded-xl shadow-md space-y-4">
        <h2 class="text-xl font-bold">Submit Order</h2>
        <form @submit.prevent="submitOrder">
            <section class="grid gap-2 grid-cols-1 sm:grid-cols-2 mb-6 w-full">
                <div>
                    <input-label for="hmo_code" value="HMO Code" />
                    <text-input id="hmo_code" v-model="form.hmo_code" required/>
                </div>

                <div>
                    <input-label for="provider_name" value="Provider Name" />
                    <text-input id="provider_name" v-model="form.provider_name" required />
                </div>

                <div>
                    <input-label for="encounter_date" value="Encounter Date" />
                    <text-input
                        type="date"
                        id="encounter_date"
                        v-model="form.encounter_date"
                        :max="maxDate"
                        required
                    />
                </div>
            </section>


            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Order Items</label>
                <div class="hidden sm:flex flex-col sm:flex-row gap-1.5">
                    <h6 class="text-sm min-w-[35%] w-full">Item</h6>
                    <h6 class="text-center text-sm min-w-[20%] w-full">Unit Price</h6>
                    <h6 class="text-sm w-full">Qty</h6>
                    <h6 class="text-center text-sm w-full">Sub Total</h6>
                    <h6 class="text-sm w-full"></h6>
                </div>

                <div v-for="(item, index) in form.items" :key="index" class="flex flex-col sm:flex-row gap-1.5 mb-8 sm:mb-2">
                    <text-input
                        placeholder="Item Name"
                        v-model="item.name"
                        class="min-w-[35%] w-full"
                        required
                    />

                    <text-input
                        type="number"
                        placeholder="Unit Price"
                        v-model="item.unit_price"
                        required
                    />

                    <text-input
                        type="number"
                        placeholder="Qty"
                        v-model="item.quantity"
                        required
                    />

                    <text-input
                        class="block border rounded-md shadow-sm bg-gray-100 pointer-events-none min-w-[18%] w-full"
                        :value="calculateSubtotal(item)"
                        placeholder="Sub total"
                        readonly
                    />

                    <danger-button data-testid="remove-order-item" @click.prevent="removeItem(index)" class="flex !p-0 justify-center font-extrabold w-full max-w-[50px]">
                        -
                    </danger-button>
                </div>

                <secondary-button @click.prevent="addItem">Add Item</secondary-button>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Order Total</label>
                <text-input
                    type="number"
                    :value="calculateOrderTotal"
                    data-testid="order-total"
                    class="bg-gray-100 text-gray-700 pointer-events-none"
                    readonly
                />
            </div>
            <primary-button
                type="submit"
            >
                Submit Order
            </primary-button>
        </form>
    </div>
</template>

<script setup>
import {computed, reactive} from 'vue';
import TextInput from "@/Components/Input.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const now = new Date();
const maxDate = now.toISOString().substring(0, 10);

const form = reactive({
    hmo_code: null,
    provider_name: null,
    encounter_date: null,
    items: [{
        name: null,
        unit_price: null,
        quantity: 1,
    }],
});

const calculateSubtotal = (item) => {
    return (item.unit_price && item.quantity) ? item.unit_price * item.quantity : '';
};

const calculateOrderTotal = computed(() => {
    return form.items.reduce((total, item) => total + item.unit_price * item.quantity, 0);
});

const addItem = () => form.items.push({
    name: null,
    unit_price: null,
    quantity: 1,
});
const removeItem = (index) => form.items.splice(index, 1);

// Handle form submission
const submitOrder = async () => {
    const data = {
        hmo_code: form.hmo_code,
        provider_name: form.provider_name,
        encounter_date: form.encounter_date,
        items: JSON.parse(JSON.stringify(form.items)),
    };

    console.log('data', data);
}
</script>
