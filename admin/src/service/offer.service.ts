import instance from "./api.service";

const deleteOffer = async (offerId) => {
    await instance.delete(`/offer/delete/${offerId}`);
}

const updateOffer = async (offerId, offer) => {
    await instance.post(`/offer/edit/${offerId}`, offer);
}

const addOffer = async (offer) => {
    const response = await instance.post('/offer/add', offer);
    return response;
}

const OfferService = {
    deleteOffer,
    updateOffer,
    addOffer,
};

export default OfferService;