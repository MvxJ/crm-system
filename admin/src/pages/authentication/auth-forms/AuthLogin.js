import React, { useRef, useState } from 'react';

import {
  Button,
  Checkbox,
  FormControlLabel,
  FormHelperText,
  Grid,
  IconButton,
  InputAdornment,
  InputLabel,
  OutlinedInput,
  Stack,
  Typography,
} from '@mui/material';

import * as Yup from 'yup';
import { Formik } from 'formik';
import AnimateButton from 'components/@extended/AnimateButton';
import { EyeOutlined, EyeInvisibleOutlined } from '@ant-design/icons';
import AuthService from 'utils/auth';

import { notification } from 'antd';
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import axios from '../../../../node_modules/axios/index';
import { jwtDecode } from '../../../../node_modules/jwt-decode/build/cjs/index';
import { isAuthenticated } from 'utils/Guard';
import { Spin } from '../../../../node_modules/antd/es/index';

const AuthLogin = () => {
  const [checked, setChecked] = useState(localStorage.getItem('rememberMe') === 'true');
  const [showPassword, setShowPassword] = useState(false);
  const formikRef = useRef();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  const handleClickShowPassword = () => {
    setShowPassword(!showPassword);
  };

  const handleMouseDownPassword = (event) => {
    event.preventDefault();
  };

  const handleLogin = (event) => {
    setLoading(true);
    event.preventDefault();
    const { email, password } = formikRef.current.values;

    if (email != '' && password != '') {
      axios.post('http://localhost:8000/api/login/check', {
        username: email,
        password: password
      }).then(response => {
        const decoded = jwtDecode(response.data.token);
        AuthService.login(response.data.token, response.data.refresh_token, response.data.user, decoded.exp);
        setLoading(false);
        navigate("/");

        notification.success({
          message: 'Login Successful',
          description: 'You have successfully logged in.',
          type: "success",
          placement: 'bottomRight'
        });

        if (checked) {
          localStorage.setItem('username', email);
          localStorage.setItem('password', password);
          localStorage.setItem("rememberMe", true);
        } else {
          localStorage.removeItem('username');
          localStorage.removeItem('password');
          localStorage.setItem("rememberMe", false);
        }
      }).catch(e => {
        if (e.response.status == 403) {
          setLoading(false);
          notification.error({
            message: 'Login Error',
            description: 'Access denied. Please check your credentials.',
            type: "error",
            placement: 'bottomRight'
          });
        } else {
          setLoading(false);
          notification.error({
            message: 'Login Error',
            description: 'Bad credentials please try again.',
            type: "error",
            placement: 'bottomRight'
          });
        }
      }).finally(() => {
        setLoading(false);
      });
    } else {
    }
  }


  return (
    <>
      <Spin
        spinning={loading}
        tip="Loading..."
      >
        <Formik
          innerRef={formikRef}
          initialValues={{
            email: localStorage.getItem('username') || '',
            password: localStorage.getItem('password') || '',
            submit: null,
          }}
          validationSchema={Yup.object().shape({
            email: Yup.string().max(255).required('Email is required'),
            password: Yup.string().max(255).required('Password is required'),
          })}
        >
          {({ errors, handleBlur, handleChange, isSubmitting, touched, values }) => (
            <form noValidate>
              <Grid container spacing={3}>
                <Grid item xs={12}>
                  <Stack spacing={1}>
                    <InputLabel htmlFor="email-login">Username</InputLabel>
                    <OutlinedInput
                      id="email-login"
                      type="text" // Change to text, as it's a username
                      value={values.email}
                      name="email"
                      onBlur={handleBlur}
                      onChange={handleChange}
                      placeholder="Enter username"
                      fullWidth
                      error={Boolean(touched.email && errors.email)}
                    />
                    {touched.email && errors.email && (
                      <FormHelperText error id="standard-weight-helper-text-email-login">
                        {errors.email}
                      </FormHelperText>
                    )}
                  </Stack>
                </Grid>
                <Grid item xs={12}>
                  <Stack spacing={1}>
                    <InputLabel htmlFor="password-login">Password</InputLabel>
                    <OutlinedInput
                      fullWidth
                      error={Boolean(touched.password && errors.password)}
                      id="password-login"
                      type={showPassword ? 'text' : 'password'}
                      value={values.password}
                      name="password"
                      onBlur={handleBlur}
                      onChange={handleChange}
                      endAdornment={
                        <InputAdornment position="end">
                          <IconButton
                            aria-label="toggle password visibility"
                            onClick={handleClickShowPassword}
                            onMouseDown={handleMouseDownPassword}
                            edge="end"
                            size="large"
                          >
                            {showPassword ? <EyeOutlined /> : <EyeInvisibleOutlined />}
                          </IconButton>
                        </InputAdornment>
                      }
                      placeholder="Enter password"
                    />
                    {touched.password && errors.password && (
                      <FormHelperText error id="standard-weight-helper-text-password-login">
                        {errors.password}
                      </FormHelperText>
                    )}
                  </Stack>
                </Grid>

                <Grid item xs={12} sx={{ mt: -1 }}>
                  <Stack direction="row" justifyContent="space-between" alignItems="center" spacing={2}>
                    <FormControlLabel
                      control={
                        <Checkbox
                          checked={checked}
                          onChange={(event) => setChecked(event.target.checked)}
                          name="checked"
                          color="primary"
                          size="small"
                        />
                      }
                      label={<Typography variant="h6">Remember me</Typography>}
                    />
                  </Stack>
                </Grid>
                {errors.submit && (
                  <Grid item xs={12}>
                    <FormHelperText error>{errors.submit}</FormHelperText>
                  </Grid>
                )}
                <Grid item xs={12}>
                  <AnimateButton>
                    <Button disableElevation disabled={isSubmitting} fullWidth size="large" type="submit" variant="contained" color="primary" onClick={handleLogin}>
                      Login
                    </Button>
                  </AnimateButton>
                </Grid>
              </Grid>
            </form>
          )}
        </Formik>
      </Spin>
    </>
  );
};

export default AuthLogin;
