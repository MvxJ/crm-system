import React from "react";
import { Navigate, Outlet } from "react-router-dom";

export type ProtectedRouteProps = React.PropsWithChildren<{
  isAllowed: boolean;
  redirectTo?: string;
}>;

export const ProtectedRoute: React.FC<ProtectedRouteProps> = ({
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
