import { useEffect, useState } from "react";
import instance from "utils/api";
import {
  EyeInvisibleOutlined,
  EyeOutlined,
  DeleteOutlined,
  HighlightOutlined,
  UserOutlined,
} from "@ant-design/icons";
import { Button, Form, Input } from "antd";
import "./TabStyles.css";
import {
  Col,
  Popconfirm,
  Row,
  Spin,
  Tooltip,
  notification,
} from "../../node_modules/antd/es/index";
import AuthService from "utils/auth";
import FormatUtils from "utils/format-utils";
import { Link } from "../../node_modules/react-router-dom/dist/index";

const ServiceRequestCommentsTab = ({ serviceRequestId }) => {
  const [data, setData] = useState([]);
  const [pageItems, setPageItems] = useState(12);
  const [loading, setLoading] = useState(false);
  const [commentData, setCommentData] = useState({
    message: "",
  });
  const { TextArea } = Input;
  const [currentUser, setCurrentUser] = useState(null);
  const [maxResults, setMaxResults] = useState(0);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setCommentData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  const fetchData = async (items) => {
    setLoading(true);
    setData([]);

    try {
      const response = await instance.get(
        `/comment/${serviceRequestId}/list?items=${items}&order=ASC&orderBy=createdDate`
      );

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch comments.",
          type: "error",
          placement: "bottomRight",
        });
        return;
      }

      if (response.data.data == null) {
        setLoading(false);
        return;
      }

      const newData = response.data.data.comments
        ? response.data.data.comments
        : [];

      setMaxResults(response.data.data ? response.data.data.maxResults : 0);
      setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch comments.",
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

  const handleAddComment = async () => {
    try {
      if (commentData.message.length == 0) {
        notification.error({
          message: "Can't post comment.",
          description: "Please fill message.",
          type: "error",
          placement: "bottomRight",
        });

        return;
      }

      const response = await instance.post(`/comment/add`, {
        user: parseInt(currentUser.id),
        serviceRequest: parseInt(serviceRequestId),
        message: commentData.message,
        isHidden: false,
      });

      if (response.status != 200) {
        notification.error({
          message: "Can't post comment.",
          type: "error",
          placement: "bottomRight",
        });

        return;
      }

      commentData.message = null;

      notification.success({
        message: "Successfully added comment.",
        type: "success",
        placement: "bottomRight",
      });

      fetchData(pageItems);
    } catch (e) {
      notification.error({
        message: "Can't post comment.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  const handleDeleteComment = async (commentId) => {
    try {
      const response = await instance.delete(`/comment/${commentId}/delete`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't delete comment.",
          type: "error",
          placement: "bottomRight",
        });
        return;
      }

      notification.success({
        message: "Comment deleted successfully.",
        type: "success",
        placement: "bottomRight",
      });

      fetchData(pageItems);
    } catch (e) {
      notification.error({
        message: "Can't delete comment.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  const handleToggleHideComment = async (commentId, hidden) => {
    try {
      const response = await instance.patch(`/comment/${commentId}/edit`, {
        isHidden: hidden,
      });

      if (response.status !== 200) {
        notification.error({
          message: "Can't toggle comment visibility.",
          type: "error",
          placement: "bottomRight",
        });
        return;
      }

      notification.success({
        message: "Comment visibility toggled successfully.",
        type: "success",
        placement: "bottomRight",
      });

      fetchData(pageItems);
    } catch (e) {
      notification.error({
        message: "Can't toggle comment visibility.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  useEffect(() => {
    const user = AuthService.getCurrentUser();
    setCurrentUser(user);
    fetchData(pageItems);
  }, [serviceRequestId]);

  return (
    <>
      <Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Col span={24}>
            <Form.Item label={"Comment"}>
              <TextArea
                showCount
                maxLength={1500}
                style={{ height: 100, resize: "none" }}
                placeholder="Comment..."
                value={commentData.message}
                onChange={handleInputChange}
                name="message"
              />
            </Form.Item>
            <Form.Item style={{ textAlign: "right", width: "100%" }}>
              <Button onClick={handleAddComment} type="primary">
                <HighlightOutlined /> Add comment
              </Button>
            </Form.Item>
          </Col>
        </Form>
      </Row>
      {loading ? (
        <Row>
          <Col span={24} style={{ textAlign: "center" }}>
            <Spin />
          </Col>
        </Row>
      ) : (
        <div>
          <Row className="number-of-showing" style={{ marginBottom: "5px" }}>
            Showing {data.length} of {maxResults}
          </Row>
          <Row>
            <Col span={24}>
              {data.length > 0
                ? data.map((comment) => (
                    <Row key={comment.id}>
                      <Col span={24}>
                        <Row>
                          <Col span={12}>
                            <Row style={{ fontSize: "14px" }}>
                              <Link
                                to={`/administration/users/detail/${comment.author.id}`}
                              >
                                <UserOutlined /> {comment.author.email}
                              </Link>
                            </Row>
                            <Row style={{ fontSize: "11px" }}>
                              <i>
                                {comment.createdAt
                                  ? FormatUtils.formatDateWithTime(
                                      comment.createdAt.date
                                    )
                                  : "-"}
                              </i>
                            </Row>
                          </Col>
                          <Col span={12} style={{ textAlign: "right" }}>
                            {currentUser &&
                              currentUser.id === comment.author.id && (
                                <div
                                  style={{
                                    display: "flex",
                                    justifyContent: "flex-end",
                                  }}
                                >
                                  <Tooltip
                                    title={
                                      comment.isHidden === true
                                        ? "Show Comment"
                                        : "Hide Comment"
                                    }
                                  >
                                    <Button
                                      type="text"
                                      onClick={() =>
                                        handleToggleHideComment(
                                          comment.id,
                                          !comment.isHidden
                                        )
                                      }
                                    >
                                      {comment.isHidden === true ? (
                                        <EyeOutlined />
                                      ) : (
                                        <EyeInvisibleOutlined />
                                      )}{" "}
                                      {comment.isHidden === true
                                        ? "Show Comment"
                                        : "Hide Comment"}
                                    </Button>
                                  </Tooltip>
                                  <Tooltip title="Delete Comment">
                                    <Popconfirm
                                      title="Are you sure you want to delete this comment?"
                                      onConfirm={() =>
                                        handleDeleteComment(comment.id)
                                      }
                                    >
                                      <Button
                                        type="text"
                                        danger
                                        style={{ marginLeft: "5px" }}
                                      >
                                        <DeleteOutlined /> Delete
                                      </Button>
                                    </Popconfirm>
                                  </Tooltip>
                                </div>
                              )}
                          </Col>
                          <Col span={24}>
                            {(comment.isHidden === true &&
                              currentUser.id === comment.author.id) ||
                            comment.isHidden == false ? (
                              <div>
                                <p>{comment.message}</p>
                              </div>
                            ) : (
                              <div>
                                <p><i>Hidden comment</i></p>
                              </div>
                            )}
                          </Col>
                        </Row>
                      </Col>
                    </Row>
                  ))
                : null}
            </Col>
          </Row>
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

export default ServiceRequestCommentsTab;