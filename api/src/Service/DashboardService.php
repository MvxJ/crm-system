<?php

namespace App\Service;

use App\Entity\Role;
use App\Helper\DashboardStatisticsHelper;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardService
{
    private DashboardStatisticsHelper $statisticsHelper;

    public function __construct(DashboardStatisticsHelper $statisticsHelper)
    {
        $this->statisticsHelper = $statisticsHelper;
    }

    public function getDashboardStatistics(UserInterface $user): ?array
    {
        $statistics = [];
        $masterRole = $this->voteMasterRole($user);

        switch ($masterRole) {
            case Role::ROLE_ADMIN:
                $statistics = $this->statisticsHelper->getAdminStatistics();
                break;
            case Role::ROLE_SERVICE:
                $statistics = $this->statisticsHelper->getTechnicianStatistics();
                break;
            case Role::ROLE_ACCOUNTMENT:
                $statistics = $this->statisticsHelper->getStuffStatistics();
                break;
            default:
                $statistics = $this->statisticsHelper->getPublicStatistics();
                break;
        }

        return [
            'assignedStatisticForRole' => $masterRole,
            'analytic' => $statistics
        ];
    }

    private function voteMasterRole(UserInterface $user): string
    {
        $roles = $user->getRoles();

        if (in_array(Role::ROLE_ADMIN, $roles)) {
            return Role::ROLE_ADMIN;
        }

        if (in_array(Role::ROLE_SERVICE, $roles)) {
            return Role::ROLE_SERVICE;
        }

        if (in_array(Role::ROLE_ACCOUNTMENT, $roles)) {
            return Role::ROLE_ACCOUNTMENT;
        }

        return Role::ROLE_ACCESS_ADMIN_PANEL;
    }
}