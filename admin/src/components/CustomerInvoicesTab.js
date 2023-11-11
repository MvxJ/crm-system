import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { DownloadOutlined, EyeOutlined, MoreOutlined } from '@ant-design/icons';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import './TabStyles.css';
import FormatUtils from 'utils/format-utils';
import { useNavigate } from '../../node_modules/react-router-dom/dist/index';

const CustomerIncoicesTab = ({ customerId }) => {
  const [data, setData] = useState([]);
  const [pageItems, setPageItems] = useState(12);
  const [loading, setLoading] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [currentId, setCurrentId] = useState(null);
  const [maxResults, setMaxResults] = useState(0);
  const navigate = useNavigate();

  const fetchData = async (items) => {
    setLoading(true);
    setData([]);

    try {
      const response = await instance.get(`/bill/list?customerId=${customerId}&items=${items}`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch user invoices.",
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

      const newData = response.data.results ? response.data.results.bills : [];

      setMaxResults(response.data.results ? response.data.results.maxResults : 0);
      setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch user invoices.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }

    setLoading(false);
  };

  const handleFetchMore = () => {
    const maxItems = pageItems + 12;
    setPageItems(maxItems);

    fetchData(maxItems);
  };

  const downloadPDF = async (fileName, action) => {
    try {
      const encodedFileName = encodeURIComponent(fileName);
      const downloadUrl = `/file/download?file=${encodedFileName}`;
      const { data } = await instance.get(downloadUrl, { responseType: 'blob', });

      if (action == 'open') {
        openPdfInNewTab(data);
      } else if (action == 'download') {
        downloadFile(data, fileName);
      }

    } catch (e) {
      notification.error({
        message: "There was an error while downloading file.",
        description: e.message,
        placement: "bottomRight",
        type: "error"
      })
    }
  }

  const openPdfInNewTab = (pdfContent) => {
    const blob = new Blob([pdfContent], { type: 'application/pdf' });
    const dataUrl = URL.createObjectURL(blob);
    window.open(dataUrl, '_blank');
  };

  const downloadFile = (pdfContent, fileName) => {
    const blob = new Blob([pdfContent], { type: 'application/pdf' });
    const dataUrl = URL.createObjectURL(blob);
    const anchor = document.createElement('a');

    anchor.href = dataUrl;
    anchor.download = FormatUtils.extractFileName(fileName);
    anchor.click();
  }

  const handleClick = (event, item) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentId(item.id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
  };

  const openDetailBillPage = () => {
    navigate(`/office/invoices/detail/${currentId}`);
  }

  useEffect(() => {
    fetchData(pageItems);
  }, [customerId]);

  return (
    <>
      {loading ? (
        <Row>
          <Col span={24} style={{ textAlign: 'center' }}>
            <Spin />
          </Col>
        </Row>
      ) : (
        <div>
          <Row className="number-of-showing">
            Showing {data.length} of {maxResults}
          </Row>
          <List
            dataSource={data}
            renderItem={(item) => (
              <List.Item key={item.id}>
                <div style={{ width: "100%"}}>
                  <Row style={{display: 'flex', justifyContent: 'center'}}>
                    <Col span={23}>
                      <Row>
                        <Col>
                          <div className='status-badge' style={{ backgroundColor: FormatUtils.getBillBadgeDetails(item.status).color }}>
                            {FormatUtils.getBillBadgeDetails(item.status).text}
                          </div>
                        </Col>
                        <Col>
                          <b>File name:</b> {FormatUtils.extractFileName(item.fileName)} &nbsp;
                        </Col>
                        <Col>
                          <b>Issued At:</b> {FormatUtils.formatDateWithTime(item.dateOfIssue.date)} &nbsp;
                        </Col>
                      </Row>
                    </Col>
                    <Col span={1}>
                      <div className="actionColumn">
                        <MoreOutlined onClick={(event) => handleClick(event, item)} />
                        <Menu
                          anchorEl={anchorEl}
                          open={Boolean(anchorEl)}
                          onClose={handleClose}
                          onClick={handleClose}
                          PaperProps={{
                            style: {
                              boxShadow: '0px 2px 4px rgba(0, 0, 0, 0.1)',
                            },
                          }}
                        >
                          <MenuItem onClick={openDetailBillPage}>
                            <Link className="text-decoration-none">
                              <EyeOutlined /> Invoice Detail
                            </Link>
                          </MenuItem>
                          <MenuItem>
                            <Link onClick={() => downloadPDF(item.fileName, 'open')} className="text-decoration-none">
                              <EyeOutlined /> View File
                            </Link>
                          </MenuItem>
                          <MenuItem>
                            <Link onClick={() => downloadPDF(item.fileName, 'download')} className="text-decoration-none">
                              <DownloadOutlined /> Download File
                            </Link>
                          </MenuItem>
                        </Menu>
                      </div>
                    </Col>
                  </Row>
                </div>
              </List.Item>
            )}
          />
          {
            data.length < maxResults ?
              <div style={{ textAlign: 'center', width: '100%' }}>
                <a onClick={handleFetchMore}>Fetch More Data</a>
              </div>
              : null
          }
        </div>
      )}
    </>
  );
};

export default CustomerIncoicesTab;