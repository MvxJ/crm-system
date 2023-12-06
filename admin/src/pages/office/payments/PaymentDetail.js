import MainCard from 'components/MainCard';
import { Link, useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Row, Col, notification, Button, Spin } from '../../../../node_modules/antd/es/index';
import FormatUtils from 'utils/format-utils';
import instance from 'utils/api';
import { EditOutlined } from '@ant-design/icons';
import './Payment.css';

const PaymentDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [payment, setPayment] = useState({
    id: id,
		status: null,
		amount: 0,
		paidBy: 0,
		note: null,
		createdAt: '',
		customer: null,
		bill: null
  });

  const styles = {
    userInicials: {
      width: '35px',
      height: '35px',
      borderRadius: '50%',
      border: '1px solid #1890ff',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      backgroundColor: '#e6f7ff',
      color: '#1890ff',
      fontWeight: 'bold'
    }
  }

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await instance.get(`/payments/${id}/detail`);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          type: 'error',
          messsage: "Can't fetch payment detail.",
          placement: 'bottomRight'
        });

        return;
      }

      setPayment({
        id: id,
        status: response.data.payment.status,
        amount: response.data.payment.amount,
        paidBy: response.data.payment.paidBy,
        note: response.data.payment.note,
        createdAt: response.data.payment.createdAt,
        customer: response.data.payment.customer,
        bill: response.data.payment.bill
      });
      setLoading(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        type: 'error',
        messsage: "Can't fetch payment detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const getUserInicials = () => {
    if (payment.customer) {
      const name = payment.customer.name;
      const surname = payment.customer.surname;

      return name.slice(0, 1).toUpperCase() + surname.slice(0, 1).toUpperCase();
    }

    return '';
  }

  const handleEditClick = () => {
    navigate(`/office/payments/edit/${id}`);
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
  <>
    <Spin spinning={loading}>
    <MainCard title={
      <div className='title-container'>
      <span>{'Payment Detail #' + id}</span>
      <div className='badge' style={{ backgroundColor: FormatUtils.getPaymentStatusBadge(payment.status).color }}>
        {FormatUtils.getPaymentStatusBadge(payment.status).text}
      </div>
    </div>
    }>
      <Row style={{textAlign: 'right'}}>
        <Col span={24}>
          { payment.status < 1 ? 
          <Button type="primary" onClick={handleEditClick}>
            <EditOutlined /> Edit Payment
          </Button>
          : null }
        </Col>
      </Row>
      <Row>
        <Col span={24}>
          <b>Amount: </b> {payment.amount}
        </Col>
        <Col span={24}>
          <b>Paid By: </b> {FormatUtils.getPaidBy(payment.paidBy)}
        </Col>
        <Col span={24}>
          <b>Payment Date : </b> {FormatUtils.formatDateWithTime(payment.createdAt.date)}
        </Col>
      </Row>
      <Row style={{marginTop: '15px', marginBottom: '15px'}}>
        <Col span={11}>
          <Row>
            <Col span={24}>
              <b>Customer:</b>
            </Col>
            <Col span={24} style={{textAlign: 'left'}}>
            <Link to={`/customers/detail/${payment.customer?.id}`}>
                  <div style={{ display: 'flex', alignItems: 'center', marginTop: '5px'}}>
                    <div style={styles.userInicials}>
                      {getUserInicials(payment.customer)}
                    </div>
                    <div style={{ marginLeft: '5px' }}>
                      {payment.customer?.name + ' ' + payment.customer?.surname}
                    </div>
                  </div>
                </Link>
            </Col>
          </Row>
        </Col>
        <Col span={11} offset={2}>
          <Col span={24}>
            <b>Invoice: </b>
          </Col>
          <Col span={24}>
            <Link to={`/office/invoices/detail/${payment.bill?.id}`}>
              #{payment.bill?.number}
            </Link>
          </Col>
        </Col>
      </Row>
      <Row>
        <Col span={24}>
          <b>Note: </b>
        </Col>
        <Col span={24}>
          { payment.note != null && payment.note.length > 0 ? payment.note : 'empty' }
        </Col>
      </Row>
    </MainCard>
    </Spin>
  </>
)};

export default PaymentDetail