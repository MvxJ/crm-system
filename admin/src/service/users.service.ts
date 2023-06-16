import instance from "./api.service";

const deleteUser = async (userId) => {
    await instance.delete(`/users/delete/${userId}`);
}

const updateUser = async (userId, user) => {
    await instance.post(`/users/edit/${userId}`, user);
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