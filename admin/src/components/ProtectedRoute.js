import React from "react";
import { Navigate, Outlet } from "react-router-dom";

export const ProtectedRoute = ({
  isAllowed,
  redirectTo = '/login',
  children,
}) => {
  return !isAllowed ? (
    <Navigate to={redirectTo} replace />
  ) : children ? (
    <>{children}</>
  ) : (
    <Outlet />
  );
};