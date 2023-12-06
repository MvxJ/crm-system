import React, { useEffect, useState } from "react";
import { List, Spin } from "antd";
import instance from "utils/api";
import { notification } from "antd";
import { Col, Row } from "../../node_modules/antd/es/index";
import { EyeOutlined, MoreOutlined } from "@ant-design/icons";
import { Menu, MenuItem } from "@mui/material";
import "./TabStyles.css";
import {
  Link,
  useNavigate,
} from "../../node_modules/react-router-dom/dist/index";
import FormatUtils from "utils/format-utils";

const CustomerMessagesTab = ({ customerId }) => {
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
      const response = await instance.get(
        `/messages/list?customerId=${customerId}&items=${items}&order=DESC&orderBy=createdDate`
      );

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch user messages.",
          type: "error",
          placement: "bottomRight",
        });
        return;
      }

      const newData = response.data.results
        ? response.data.results.messages
        : [];

      setMaxResults(
        response.data.results ? response.data.results.maxResults : 0
      );
      setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch user invoices.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    }

    setLoading(false);
  };

  const handleFetchMore = async () => {
    const maxResults = 12 + pageItems;
    setPageItems(maxResults);

    fetchData(maxResults);
  };

  const handleMessageDetail = () => {
    navigate(`/messages/detail/${currentId}`);
  };

  const handleClick = (event, item) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentId(item.id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
  };

  useEffect(() => {
    fetchData(pageItems);
  }, [customerId]);

  return (
    <>
      {loading ? (
        <Row>
          <Col span={24} style={{ textAlign: "center" }}>
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
                        <Col className='status-badge' style={{ backgroundColor: FormatUtils.getMessageBadgeObj(item.type).color }}>
                          {FormatUtils.getMessageBadgeObj(item.type).text}
                        </Col>
                        <Col>
                          <b>Subject:</b> {item.subject}&nbsp;
                        </Col>
                        <Col>
                          <b>Send date:</b> {item.createdAt.date}&nbsp;
                        </Col>
                      </Row>
                    </Col>
                    <Col span={1}>
                      <div className="actionColumn">
                        <MoreOutlined
                          onClick={(event) => handleClick(event, item)}
                        />
                        <Menu
                          anchorEl={anchorEl}
                          open={Boolean(anchorEl)}
                          onClose={handleClose}
                          onClick={handleClose}
                          PaperProps={{
                            style: {
                              boxShadow: "0px 2px 4px rgba(0, 0, 0, 0.1)",
                            },
                          }}
                        >
                          <MenuItem onClick={handleMessageDetail}>
                            <Link className="text-decoration-none">
                              <EyeOutlined /> Message Details
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
          {data.length < maxResults ? (
            <div style={{ textAlign: "center", width: "100%" }}>
              <a onClick={handleFetchMore}>Fetch More Data</a>
            </div>
          ) : null}
        </div>
      )}
    </>
  );
};

export default CustomerMessagesTab;
