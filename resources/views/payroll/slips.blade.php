<x-app-layout>

    <div class="container-fluid px-0">
        <div class="row g-2 mb-3">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-calendar-week me-2 text-primary fs-4"></i> <span class="fw-bold">Period</span>
                        </h6>
                        <p id="period" class="fs-5 mt-2">February 2025</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-calendar-check me-2 text-success fs-4"></i> <span class="fw-bold">Pay
                                Day</span>
                        </h6>
                        <p id="pay-day" class="fs-5 mt-2">28th February 2025</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-people me-2 text-info fs-4"></i> <span class="fw-bold">Employees</span>
                        </h6>
                        <p id="employees" class="fs-5 mt-2">2 Employees</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-cash-stack me-2 text-warning fs-4"></i> <span class="fw-bold">Payroll
                                Cost</span>
                        </h6>
                        <p id="payroll-cost" class="fs-5 mt-2">KES 123,000</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2 text-secondary fs-4"></i> <span class="fw-bold">Net Pay</span>
                        </h6>
                        <p id="net-pay" class="fs-5 mt-2">KES 96,534</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-graph-down me-2 text-danger fs-4"></i> <span class="fw-bold">Taxes</span>
                        </h6>
                        <p id="taxes" class="fs-5 mt-2">KES 26,466</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle me-2 text-warning fs-4"></i> <span class="fw-bold">Pre-Tax
                                Deductions</span>
                        </h6>
                        <p id="pre-tax-deductions" class="fs-5 mt-2">KES 12,325</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body">
                        <h6 class="d-flex align-items-center">
                            <i class="bi bi-arrow-down-up me-2 text-info fs-4"></i> <span class="fw-bold">Post-Tax
                                Deductions</span>
                        </h6>
                        <p id="post-tax-deductions" class="fs-5 mt-2">KES 0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="container-fluid px-0">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success">Download</button>
                            <button type="button" class="btn btn-success dropdown-bs-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">ITAX</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=itax&format=csv" id="idItax" title="Export to ITax Employee Details to import to ITax Excel">ITax Employee Details (Return/Remittance) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=itax&format=xls&sub_type=schedule" id="idItax" title="KRA ITAX Return Schedule">KRA ITAX Return Schedule</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=itax&format=pdf&sub_type=schedule" id="idItax" title="KRA ITAX Return Schedule">KRA ITAX Return Schedule (PDF)</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=paye" id="idBank" title="Excel">Month by month report</a></li>

                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=paye" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=paye" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=paye" id="idBank" title="Excel">Grouped by job category</a></li>

                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">SHIF/NHIF</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nhif" id="idNhif" title="Export SHIF/NHIF report">SHIF/NHIF (Return/Remittance) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nhif&sub_type=schedule" id="idNhif" title="SHIF/NHIF Schedule">SHIF/NHIF Schedule</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nhif&format=pdf&sub_type=schedule" id="idNhif" title="SHIF/NHIF Schedule">SHIF/NHIF Schedule (PDF)</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=shif" id="idBank" title="Excel">Month by month report</a></li>

                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=shif" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=shif" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=shif" id="idBank" title="Excel">Grouped by job category</a></li>

                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">NSSF</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nssf&type=latest" id="idNssf" title="Export NSSF report">New NSSF (Return/Remittance) format</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nssf&type=new&format=csv" id="idNssf" title="Export NSSF report">Up to June 2018</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nssf&type=old&format=csv" id="idNssf" title="Export NSSF report">Old NSSF format</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nssf&type=latest&format=pdf" id="idNssf" title="Export NSSF report">Schedule (PDF)</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=nssf" id="idBank" title="Excel">Month by month report</a></li>

                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=nssf" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=nssf" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=nssf" id="idBank" title="Excel">Grouped by job category</a></li>

                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Housing Levy</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=housing_levy&format=csv" id="idHousingLevy" title="Export housing Levy report">Housing Levy (KRA Return/Remittance) import report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=housing_levy&format=xls&sub_type=schedule" id="idHousingLevy" title="payroll::payroll.view.actions.housing_levy.schedule_desc">Housing Levy Schedule</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=housing_levy&format=pdf&sub_type=schedule" id="idHousingLevy" title="payroll::payroll.view.actions.housing_levy.schedule_desc">Housing Levy report (PDF)</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=housing_levy" id="idBank" title="Excel">Month by month report</a></li>

                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=housing_levy" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=housing_levy" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=housing_levy" id="idBank" title="Excel">Grouped by job category</a></li>


                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Bank & Cash files</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=generic&format=xls" id="idBank" title="Generic / General Bank report">Generic / General Bank report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=generic&format=pdf" id="idBank" title="Generic/General Bank report (PDF)">Generic/General Bank report (PDF)</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=kcb&format=xls" id="idBank" title="payroll::payroll.view.actions.bank.kcb">KCB Bank Excel report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=kcb&format=tsv" id="idBank" title="payroll::payroll.view.actions.bank.kcb">KCB Bank Tab-Separated report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=barclays&format=csv" id="idBank" title="Barclays Bank report">Barclays Bank report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=coop&format=xls" id="idBank" title="Co-operative Bank report">Co-operative Bank report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=boa&format=xls" id="idBank" title="Bank of Africa report">Bank of Africa report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=ncba_eft&format=xls" id="idBank" title="NCBA Bank - EFT report">NCBA Bank - EFT report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=ncba_rtgs&format=xls" id="idBank" title="NCBA Bank - RTGS report">NCBA Bank - RTGS report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=equity_file&format=xls" id="idBank" title="Equity Bank file (Eazzy Remmittance) report">Equity Bank file (Eazzy Remmittance) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=equity_within&format=xls" id="idBank" title="Equity Bank (Within) report">Equity Bank (Within) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=equity_other&format=xls" id="idBank" title="Equity Bank (Other) report">Equity Bank (Other) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=equity_api&format=xls" id="idBank" title="Equity Bank (API) report">Equity Bank (API) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=dtb&format=xls" id="idBank" title="Diamond Trust Bank (DTB) report">Diamond Trust Bank (DTB) report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=im&format=xls" id="idBank" title="I&amp;M Bank report">I&amp;M Bank report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=bank&bank=cash&format=csv" id="idBank" title="Cash report">Cash report</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Master roll</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls" id="idMasterRoll" title="Detailed in Excel">Detailed in Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&orientation=landscape&page_size=A0" target="_blank" title="Detailed in PDF">Detailed in PDF</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=csv&view_type=summary" id="idMasterRoll" title="Summary in Excel">Summary in Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&view_type=summary&orientation=landscape" target="_blank" title="Summary in PDF">Summary in PDF</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&group_by=location_id_text" id="idMasterRoll" title="Group detailed excel by location">Group detailed excel by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&group_by=department_id_text" id="idMasterRoll" title="Group detailed excel by department">Group detailed excel by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&group_by=job_category_id_text" id="idMasterRoll" title="Group detailed excel by job category">Group detailed excel by job category</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&orientation=landscape&page_size=A0&group_by=location_id_text" target="_blank" title="Group detailed PDF by location">Group detailed PDF by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&orientation=landscape&page_size=A0&group_by=department_id_text" target="_blank" title="Group detailed PDF by department">Group detailed PDF by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&orientation=landscape&page_size=A0&group_by=job_category_id_text" target="_blank" title="Group detailed PDF by job category">Group detailed PDF by job category</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&view_type=summary&group_by=location_id_text" id="idMasterRoll" title="Group summary excel by location">Group summary excel by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&view_type=summary&group_by=department_id_text" id="idMasterRoll" title="Group summary excel by department">Group summary excel by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=xls&view_type=summary&group_by=job_category_id_text" id="idMasterRoll" title="Group summary excel by job category">Group summary excel by job category</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&view_type=summary&orientation=landscape&group_by=location_id_text" target="_blank" title="Group summary PDF by location">Group summary PDF by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&view_type=summary&orientation=landscape&group_by=department_id_text" target="_blank" title="Group summary PDF by department">Group summary PDF by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&view_type=summary&orientation=landscape&group_by=job_category_id_text" target="_blank" title="Group summary PDF by job category">Group summary PDF by job category</a></li>

                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Company payslip</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=csv&view_type=company_payslip" id="idP9" title="Excel">Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=pdf&view_type=company_payslip" target="_blank" title="PDF">PDF</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">P9 report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/p9/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5&export=csv" id="idP9" title="Excel">Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/p9/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=5" target="_blank" title="PDF">PDF</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">NITA report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nita&format=csv&type=kra" id="idNita" title="Export NITA report">NITA (KRA Return/Remittance) import report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nita&format=xls" id="idNita" title="Export NITA report">NITA report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=nita&format=pdf" id="idNita" title="Export NITA report">NITA report (PDF)</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">FBT report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=fbt&format=csv" id="idP9" title="KRA FBT CSV report">KRA FBT CSV report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=fbt&format=xls&sub_type=schedule" target="_blank" title="FBT Excel report">FBT Excel report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=fbt&format=pdf&sub_type=schedule" target="_blank" title="FBT PDF report">FBT PDF report</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Welfare report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=Welfare" id="idBank" title="Excel">Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&format=pdf&export=Welfare" id="idBank" title="PDF">PDF</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=monthly&format=xls&deduction_id=987" id="idBank" title="Excel">This month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=annual&format=xls&deduction_id=987" id="idBank" title="Excel">This year report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=987" id="idBank" title="Excel">Month by month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=987" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=987" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=987" id="idBank" title="Excel">Grouped by job category</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Loan report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=Loan" id="idBank" title="Excel">Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&format=pdf&export=Loan" id="idBank" title="PDF">PDF</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=monthly&format=xls&deduction_id=2551-1" id="idBank" title="Excel">This month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=annual&format=xls&deduction_id=2551-1" id="idBank" title="Excel">This year report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=2551-1" id="idBank" title="Excel">Month by month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=2551-1" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=2551-1" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=2551-1" id="idBank" title="Excel">Grouped by job category</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a tabindex="-1" href="#">Leave Pay report</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&export=Leave Pay" id="idBank" title="Excel">Excel</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/list/payroll?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&format=pdf&export=Leave Pay" id="idBank" title="PDF">PDF</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=monthly&format=xls&deduction_id=1081" id="idBank" title="Excel">This month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=annual&format=xls&deduction_id=1081" id="idBank" title="Excel">This year report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=month_by_month&format=xls&deduction_id=1081" id="idBank" title="Excel">Month by month report</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_department&format=xls&deduction_id=1081" id="idBank" title="Excel">Grouped by department</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_location&format=xls&deduction_id=1081" id="idBank" title="Excel">Grouped by location</a></li>
                                        <li><a href="https://sapamaerp.com/payroll/statement/deducted?month=3&year=2025&user_id=&location_id=&department_id=&job_category_id=&view_depth=3&type=by_job_category&format=xls&deduction_id=1081" id="idBank" title="Excel">Grouped by job category</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="payslipsContainer">
                            {{ loader() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('modals.payslip-details')
        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const payrollId = "{{ $payroll ? $payroll->id : null }}";
                if (payrollId) {
                    getPayslips(1, payrollId);
                }
            });
        </script>
    @endpush

</x-app-layout>
