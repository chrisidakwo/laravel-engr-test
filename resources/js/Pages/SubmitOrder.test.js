import SubmitOrder from '@/Pages/SubmitOrder.vue';
import {fireEvent, render} from "@testing-library/vue";

describe('SubmitOrder.vue', () => {
    it('renders order form correctly', () => {
        const {getByText, getByLabelText, getByPlaceholderText} = render(SubmitOrder);

        // Check if form fields are rendered
        expect(getByLabelText(/HMO Code/i)).toBeInTheDocument();
        expect(getByLabelText(/Provider Name/i)).toBeInTheDocument();
        expect(getByLabelText(/Encounter Date/i)).toBeInTheDocument();
        expect(getByPlaceholderText(/Item Name/i)).toBeInTheDocument();
        expect(getByPlaceholderText(/Unit Price/i)).toBeInTheDocument();
        expect(getByPlaceholderText(/Qty/i)).toBeInTheDocument();
        expect(getByText('Add Item')).toBeInTheDocument();
    });

    it('calculates item subtotal correctly', async () => {
        const { getByPlaceholderText } = render(SubmitOrder);

        // Input values for the item
        const unitPriceInput = getByPlaceholderText(/Unit Price/i);
        const quantityInput = getByPlaceholderText(/Qty/i);
        const subtotalInput = getByPlaceholderText(/Sub total/i);

        // Set unit price and quantity
        await fireEvent.update(unitPriceInput, '130');
        await fireEvent.update(quantityInput, '2');

        // Check if the subtotal is correctly calculated
        expect(Number(subtotalInput.value)).toBe(260);
    });

    it('calculates total amount correctly when multiple items are added', async () => {
         const { getByTestId, getByText, getAllByPlaceholderText, getByLabelText } = render(SubmitOrder);

        // Add two items
        await fireEvent.click(getByText('Add Item'));
        await fireEvent.click(getByText('Add Item'));

        // Get inputs for the first item
        const [firstUnitPriceInput, secondUnitPriceInput] =
            getAllByPlaceholderText(/Unit Price/i);
        const [firstQuantityInput, secondQuantityInput] =
            getAllByPlaceholderText(/Qty/i);

        // Update values for the first item
        await fireEvent.update(firstUnitPriceInput, '150');
        await fireEvent.update(firstQuantityInput, '2');

        // Update values for the second item
        await fireEvent.update(secondUnitPriceInput, '50');
        await fireEvent.update(secondQuantityInput, '3');

        // Calculate expected total
        const totalInput = getByTestId('order-total');
        expect(Number(totalInput.value)).toBe(450);
    });

    it('removes item correctly and updates total amount', async () => {
        const { getAllByTestId, getByTestId ,getByText, getAllByPlaceholderText, getByLabelText } = render(SubmitOrder);

        // Add an item
        await fireEvent.click(getByText('Add Item'));

        // Update values for the item
        const [firstUnitPriceInput, secondUnitPriceInput] = getAllByPlaceholderText(/Unit Price/i);
        const [firstQuantityInput, secondQuantityInput] = getAllByPlaceholderText(/Qty/i);

        // Update values for the first item
        await fireEvent.update(firstUnitPriceInput, '160');
        await fireEvent.update(firstQuantityInput, '4');

        // Update values for the second item
        await fireEvent.update(secondUnitPriceInput, '350');
        await fireEvent.update(secondQuantityInput, '2');

        // Confirm that the total is 100
        const totalInput = getByTestId('order-total');
        expect(Number(totalInput.value)).toBe(1340);

        // Remove the item
        const [firstRemoveBtn, secondRemoveBtn] = getAllByTestId('remove-order-item');
        await fireEvent.click(secondRemoveBtn);

        // Confirm that the total is updated to 640
        expect(Number(totalInput.value)).toBe(640);

        await fireEvent.click(firstRemoveBtn);

        // Confirm that the total is updated to 0
        expect(Number(totalInput.value)).toBe(0);
    });
});


