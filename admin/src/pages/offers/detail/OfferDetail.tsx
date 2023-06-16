import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import { useNavigate, useParams } from "react-router-dom";
import OfferService from "../../../service/offer.service";

export interface IOfferDetailPageProps {};

const OfferDetailPage: React.FunctionComponent<IOfferDetailPageProps> = (props) => {
    const { id } = useParams();
    const [offer, setOffer] = useState([]);
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            const response = await instance.get(`/offer/${id}`);
            setOffer(response.data.offer);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const editOffer = () => {
        navigate(`/offers/edit/${offer.id}`);
    }

    const deleteOffer = async () => {
        await OfferService.deleteOffer(offer.id);
        navigate(`/offers`);
    }

    useEffect(
        () => {
            fetchData();
        }, []
    );

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    <h3>Offer #{offer.id} - {offer.title}</h3>
                </div>
                <div className="card-actions">
                    <button className="button-standard" onClick={editOffer}>
                        Edit
                    </button>
                    <button className="button-standard danger" onClick={deleteOffer}>
                        Delete
                    </button>
                </div>
            </div>
            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <h4>Price:</h4>
                        <p>{offer.price}</p>
                    </div>
                    <div className="col">
                        <h4>Discount: </h4>
                        <p>{offer.percentageDiscount}</p>
                    </div>
                </div>
                <div className="row">
                <div className="col col-100">
                        <h4>Description:</h4>
                        <p>{offer.description}</p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>Upload speed:</h4>
                        <p>{offer.uploadSpeed}</p>
                    </div>
                    <div className="col">
                        <h4>Download speed: </h4>
                        <p>{offer.downloadSpeed}</p>
                    </div>
                </div>
                <div>
                    <h4>For new users: </h4>
                    <p>{offer.forNewUsers ? 'Yes' : 'No'}</p>
                </div>
            </div>
        </div>
    );
}

export default OfferDetailPage;