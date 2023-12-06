import MainCard from 'components/MainCard';
import './Invoice.css';
import { Link, useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import {
  EyeOutlined,
  FileSyncOutlined,
  DownloadOutlined,
  UserOutlined,
  FileProtectOutlined,
  UnorderedListOutlined,
  EditOutlined
} from '@ant-design/icons';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, Table, Tag, notification } from '../../../../node_modules/antd/es/index';
import PdfUtils from 'utils/pdf-utils';
import FormatUtils from 'utils/format-utils';

const InvoiceDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [invoice, setInvoice] = useState({
    id: id,
    number: '',
    status: null,
    amount: 0,
    dateOfIssue: '',
    paymentDate: '',
    payDue: '',
    updatedAt: '',
    fileName: '',
    customer: null,
    contract: null,
    positions: []
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

  const positionColumns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name'
    },
    {
      title: 'Amount',
      dataIndex: 'amount',
      key: 'amount'
    },
    {
      title: 'Price',
      dataIndex: 'price',
      key: 'price'
    },
    {
      title: 'Description',
      dataIndex: 'description',
      key: 'description'
    },
    {
      title: 'Type',
      dataIndex: 'type',
      key: 'type',
      render: (type) => {
        let obj = { color: '', text: '' }

        switch (type) {
          case 0:
            obj.color = '#BA55D3';
            obj.text = 'Contract';
            break;
          case 1:
            obj.color = '#95de64';
            obj.text = 'Service';
            break;
          case 2:
            obj.color = '#F0E68C';
            obj.text = 'Device';
            break;
          default:
            obj.color = '#FF7F50';
            obj.text = 'Other';
            break;
        }
        return (
          <>
            <Tag color={obj.color}>
              {obj.text}
            </Tag>
          </>
        )
      }
    }
  ]

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await instance.get(`/bill/${id}/detail`);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          type: 'error',
          messsage: "Can't fetch invoice detail.",
          placement: 'bottomRight'
        });

        return;
      }

      setInvoice({
        id: id,
        number: response.data.bill.number,
        status: response.data.bill.status,
        amount: response.data.bill.amount,
        dateOfIssue: response.data.bill.dateOfIssue,
        paymentDate: response.data.bill.paymentDate,
        payDue: response.data.bill.payDue,
        updatedAt: response.data.bill.updatedAt,
        fileName: response.data.bill.fileName,
        customer: response.data.bill.customer,
        contract: response.data.bill.contract,
        positions: response.data.bill.positions
      });

      setLoading(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        type: 'error',
        messsage: "Can't fetch invoice detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const getUserInicials = () => {
    if (invoice.customer) {
      const name = invoice.customer.name;
      const surname = invoice.customer.surname;

      return name.slice(0, 1).toUpperCase() + surname.slice(0, 1).toUpperCase();
    }

    return '';
  }

  const handleEditClick = () => {
    navigate(`/office/invoices/edit/${id}`);
  }

  const handleGeneratePdf = async () => {
    try {
      setLoading(true);
      const response = await instance.post(`/bill/${id}/generatePdfFile`);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "An error occured durgin generating PDF file.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully generated PDF file.",
        type: "success",
        placement: "bottomRight"
      });

      setTimeout(() => {
        window.location.reload();
      }, 1000);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "An error occured durgin generating PDF file.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      });
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title={
        <div className='title-container'>
          <span>{'Invoice Detail #' + id}</span>
          <div className='badge' style={{ backgroundColor: FormatUtils.getBillBadgeDetails(invoice.status).color }}>
            {FormatUtils.getBillBadgeDetails(invoice.status).text}
          </div>
        </div>
      }
      >
        <Row>
          <Col span={24} style={{ textAlign: 'right' }}>
            {invoice.fileName.length == 0 ?
            <div>
              <Button type="primary" onClick={handleGeneratePdf}>
                <FileSyncOutlined /> Generate PDF
              </Button>
                              <Button type="primary" onClick={handleEditClick} style={{ marginLeft: '5px' }}>
                              <EditOutlined /> Edit Invoice
                            </Button>
                            </div>
              : null}
            {invoice.fileName.length > 0 ?
              <div>
                <Button type="primary" onClick={() => PdfUtils.downloadPDF(invoice.fileName, 'download')} style={{ marginLeft: '5px' }}>
                  <DownloadOutlined /> Download Invoice
                </Button>
                <Button type="primary" onClick={() => PdfUtils.downloadPDF(invoice.fileName, 'open')} style={{ marginLeft: '5px' }}>
                  <EyeOutlined /> Open Invoice
                </Button>
              </div>
              : null}
          </Col>
        </Row>
        <Row>
          <h4>Invoice Detail:</h4>
        </Row>
        <Row>
          <Col span={24}>
            <b>Invoice NO: </b> {invoice.number}
          </Col>
          <Col span={24}>
            <b>Amount: </b> {invoice.amount}
          </Col>
          <Col span={24}>
            <b>Issue Date: </b> {FormatUtils.formatDateWithTime(invoice.dateOfIssue.date)}
          </Col>
          <Col span={24}>
            <b>Pay Due: </b> {FormatUtils.formatDateWithTime(invoice.payDue.date)}
          </Col>
          <Col span={24}>
            <b>Paid Date: </b> {invoice.paymentDate != null ? FormatUtils.formatDateWithTime(invoice.paymentDate.date) : 'not paid'}
          </Col>
        </Row>
        <Row>
          <Col>
          </Col>
        </Row>
        <Row>
          <Col span={11}>
            <Row>
              <h4><UserOutlined /> Customer: </h4>
            </Row>
            <Row>
              <Col>
                <Link to={`/customers/detail/${invoice.customer?.id}`}>
                  <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
                    <div style={styles.userInicials}>
                      {getUserInicials(invoice.customer)}
                    </div>
                    <div style={{ marginLeft: '5px' }}>
                      {invoice.customer?.name + ' ' + invoice.customer?.surname}
                    </div>
                  </div>
                </Link>
              </Col>
            </Row>
          </Col>
          {invoice.contract != null ?
            <Col span={11} offset={2}>
              <Row>
                <h4><FileProtectOutlined /> Contract: </h4>
              </Row>
              <Row>
                <Col>
                  <Link to={`/office/contracts/detail/${invoice.contract?.id}`}>
                    #{invoice.contract?.number}
                  </Link>
                </Col>
              </Row>
            </Col>
            : null}
        </Row>
        <Row>
          <h4><UnorderedListOutlined /> Positions: </h4>
        </Row>
        <Row>
          <Col span={24}>
            <Table columns={positionColumns} size="small" dataSource={invoice.positions} bordered pagination={{
              position: ['none', 'none'],
            }} />
          </Col>
        </Row>
      </MainCard>
      </Spin>
    </>
  )
};

export default InvoiceDetail;
