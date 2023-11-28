import MainCard from "components/MainCard";
import {
  MoreOutlined,
  EyeOutlined,
  DeleteOutlined,
  CheckOutlined,
  CloseOutlined,
  AppstoreAddOutlined,
  EditOutlined,
} from "@ant-design/icons";
import { useEffect, useState } from "react";
import instance from "utils/api";
import {
  Button,
  Col,
  Row,
  Spin,
  notification,
} from "../../../../node_modules/antd/es/index";
import { DataGrid } from "@mui/x-data-grid";
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from "../../../../node_modules/react-router-dom/dist/index";
import "./Model.css";
import FormatUtils from "utils/format-utils";

const ModelsList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [currentId, setCurrentId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 25,
    page: 0,
  });
  const [anchorEl, setAnchorEl] = useState(null);

  const styles = {
    addButton: {
      textAlign: "right",
      marginBottom: "15px",
    },
  };

  const columns = [
    { field: "id", headerName: "ID", width: 50 },
    {
      field: "manufacturer",
      headerName: "Manufacturer",
      width: 300,
    },
    {
      field: "name",
      headerName: "Model Name",
      width: 300,
    },
    {
      field: "type",
      headerName: "Type",
      width: 200,
      renderCell: (params) => (
        <div className="badge">
          {FormatUtils.getDeviceType(params.row.type)}
        </div>
      ),
    },
    {
      field: "price",
      headerName: "Price",
      width: 300,
    },
    {
      field: "isDeleted",
      headerName: "Deleted",
      width: 100,
      renderCell: (params) => (
        <div
          className="badge"
          style={{
            color: params.row.isDeleted == true ? "#95de64" : "#ff7875",
            textAlign: "center",
          }}
        >
          {params.row.isDeleted == true ? <CheckOutlined /> : <CloseOutlined />}
        </div>
      ),
    },
    {
      field: "actions",
      headerName: "Actions",
      type: "actions",
      width: 100,
      renderCell: (params) => (
        <div className="actionColumn">
          <MoreOutlined
            onClick={(event) => handleClick(event, params.row.id, params)}
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
            <MenuItem>
              <Link
                to={`/service/models/detail/${currentId}`}
                className="text-decoration-none"
              >
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            {currentId !== null &&
            !data.find((row) => row.id === currentId).isDeleted ? (
              <MenuItem onClick={() => handleEditModel(currentId)}>
                <EditOutlined /> Edit Model
              </MenuItem>
            ) : null}
            {currentId !== null &&
            !data.find((row) => row.id === currentId).isDeleted ? (
              <MenuItem onClick={() => handleDeleteModel(currentId)}>
                <DeleteOutlined /> Delete Model
              </MenuItem>
            ) : null}
          </Menu>
        </div>
      ),
    },
  ];

  const showNotification = (message, type) => {
    if (type == "error") {
      notification.error({
        message: message,
        type: type,
        placement: "bottomRight",
      });
      return;
    }

    notification.success({
      message: message,
      type: type,
      placement: "bottomRight",
    });
  };

  const handlePageChange = (newModel) => {
    setPaginationModel(newModel);
  };

  const handleClick = (event, id, params) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentId(id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
  };

  const fetchData = async () => {
    try {
      setLoading(true);
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(
        `/models/list?page=${page}&items=${pageSize}`
      );
      setData(response.data.results.models ? response.data.results.models : []);
      setTotalRows(response.data.results.maxResults);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification("Error fetching models list", "error");
    } finally {
      setLoading(false);
    }
  };

  const handleAddModel = () => {
    navigate(`/service/models/add`);
  };

  const handleEditModel = (modelId) => {
    navigate(`/service/models/edit/${modelId}`);
  };

  const handleDeleteModel = async (modelId) => {
    try {
      const response = await instance.delete(`/models/${modelId}/delete`);

      if (response.status != 200) {
        showNotification("An error occured during deleting model.", "error");
      }

      showNotification("Successfully deleted model.", "success");
      fetchData();
    } catch (e) {
      showNotification("An error occured during deleting model.", "error");
    }
  };

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  return (
    <>
      <Spin spinning={loading}>
        <MainCard title="Models">
          <Row>
            <Col
              span={4}
              offset={20}
              style={styles.addButton}
              onClick={handleAddModel}
            >
              <Button type="primary">
                <AppstoreAddOutlined /> Add Model
              </Button>
            </Col>
          </Row>
          <DataGrid
            rows={data}
            columns={columns}
            pagination
            paginationMode="server"
            rowCount={totalRows}
            paginationModel={paginationModel}
            onPaginationModelChange={handlePageChange}
            pageSizeOptions={[10, 25, 50]}
          />
        </MainCard>
      </Spin>
    </>
  );
};

export default ModelsList;
