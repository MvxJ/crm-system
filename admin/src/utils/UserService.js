import instance from "./api";

const deleteUser = async (userId) => {
    return await instance.delete(`/users/${userId}/delete`);
}

const updateUser = async (userId, user) => {
    return await instance.post(`/users/edit/${userId}`, user);
}

const addUser = async (user) => {
    const response = await instance.post('/users/add', user);
    return response;
}

const UsersService = {
    deleteUser,
    updateUser,
    addUser,
};

export default UsersService;