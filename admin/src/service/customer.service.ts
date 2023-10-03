import instance from "./api.service";

const deleteCustomer = async (customerId) => {
    await instance.delete(`/customers/delete/${customerId}`);
}

const updateCustomer = async (customerId, customer) => {
    await instance.post(`/customers/edit/${customerId}`, customer);
}

const addCustomer = async (customer) => {
    const response = await instance.post('/customers/add', customer);
    return response;
}

const CustomersService = {
    deleteCustomer,
    updateCustomer,
    addCustomer,
};

export default CustomersService;