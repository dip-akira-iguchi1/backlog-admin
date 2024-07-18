<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backlogの課題一覧</title>
    <!-- <link rel="stylesheet" href="styles/reset.css"> -->
    <link rel="stylesheet" href="styles/app.css">
</head>
<body>
    <main>

        <h1>僕に関連すると思わしきBacklogの課題一覧</h1>

        <section>
            <h2 class="section-heading">課題ID一覧</h2>
            <div class="tab-container">
                <div class="tabs" role="tablist">
                    <button id="tab-1" class="tab" role="tab" aria-selected="true" aria-controls="tab-panel-1" onclick="tabIssue('#tab-panel-1')">すべて</button>
                    <button id="tab-2" class="tab" role="tab" aria-selected="false" aria-controls="tab-panel-2" onclick="tabIssue('#tab-panel-2')">RFC</button>
                    <button id="tab-3" class="tab" role="tab" aria-selected="false" aria-controls="tab-panel-3" onclick="tabIssue('#tab-panel-3')">HA_AT</button>
                </div>
                <div id="tab-panel-1" class="tab-panel" role="tabpanel" aria-labelledby="tab-1">
                </div>
                <div id="tab-panel-2" class="tab-panel is-hidden" role="tabpanel" aria-labelledby="tab-2" hidden>
                </div>
                <div id="tab-panel-3" class="tab-panel is-hidden" role="tabpanel" aria-labelledby="tab-3" hidden>
                </div>
            </div>
        </section>

        <section>
            <h2 class="section-heading">フィルタリング</h2>
            <div class="filter-container">
                <div class="filter-parts">
                    <label for="search">検索:</label>
                    <div class="search-wrapper">
                        <input type="text" id="search" placeholder="検索ワードを入力">
                    </div>
                </div>
                <div class="filter-parts">
                    <label for="assignee">担当者:</label>
                    <div class="select-wrapper">
                        <select id="assignee">
                            <option value="">すべて</option>
                        </select>
                    </div>
                </div>
                <div class="filter-parts">
                    <label for="status">状態:</label>
                    <div class="select-wrapper">
                        <select id="status">
                            <option value="">すべて</option>
                            <option value="処理中">処理中</option>
                            <option value="未対応">未対応</option>
                            <option value="処理済み">処理済み</option>
                            <option value="WfR">WfR</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <table id="backlogissue">
            <button onclick="AllDisp()">全表示</button><br>

            <input type="text" id="search" placeholder="検索（未実装）"><br>

            <button onclick="disp()">HATA_FRONT表示</button><br>

            <button onclick="showItems()">開始・期限・リリース日表示非表示</button><br>

            <button onclick="showParents()">RFC&HA_AT表示（HATA_FRONT無し）</button><br>

            <button onclick="showParentsHasChild()">RFC&HA_AT表示（HATA_FRONT有り）</button><br>

            <button onclick="CopyAsScrum()">スクスク形式でコピーできる</button><br>

            <button onclick="CopyAsMpMtg()">MP定例資料形式でコピーできる</button><br>
            <thead>
                <tr>
                    <th>課題ID</th>
                    <th>課題名</th>
                    <th>担当者</th>
                    <th class='none'>状態</th>
                    <th class="none">開始日</th>
                    <th class="none">期限日</th>
                    <th>リリース日</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </main>
    <style>
        .none {
            display: none;
        }

        .on {
            display: table-cell;
        }
    </style>
    <script type="text/javascript" src="js/backlogissue.js?<?= mt_rand() ?>"></script>

    <script type="text/javascript">
        const statusName = [{
                '未対応': '未対応'
            },
            {
                'Ready': '開発待ち'
            },
            {
                '処理中': '開発中'
            }, {
                '処理済み': '開発・テスト完了'
            }, {
                '完了': '完了'
            }, {
                '受入テスト中': '受入テスト中'
            }, {
                'WfR': 'リリース待ち'
            }, {
                'テスト中': '単体結合テスト中'
            }
        ]

        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab');
            const tabPanels = document.querySelectorAll('.tab-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(tab => tab.setAttribute('aria-selected', 'false'));
                    tabPanels.forEach(panel => panel.classList.add('is-hidden'));
                    tab.setAttribute('aria-selected', 'true');
                    const selectedTabPanel = document.getElementById(tab.getAttribute('aria-controls'));
                    selectedTabPanel.classList.remove('is-hidden');
                });
            });
        });

        function AllDisp() {
            const trs = document.querySelectorAll('#backlogissue tbody tr');
            trs.forEach(tr => {
                tr.style.display = 'table-row';
            })
        }
        const disp = () => {
            const trs = document.querySelectorAll('#backlogissue tbody tr');
            trs.forEach(tr => {
                if (tr.children[0].textContent.includes('HATA_FRONT')) {
                    tr.style.display = 'table-row';
                } else {
                    tr.style.display = 'none';
                }
            });
        }

        function showParentsHasChild() {
            document.querySelectorAll('#backlogissue tbody tr')?.forEach(tr => {
                if (tr.style.backgroundColor !== '' && (tr.children[0].textContent.includes('HA_AT-') || tr.children[0].textContent.includes('RFC-'))) {
                    tr.style.display = 'table-row';
                } else {
                    tr.style.display = 'none';
                }
            });
        }

        function showParents() {

            document.querySelectorAll('#backlogissue tbody tr')?.forEach(tr => {

                if (tr.children[0].textContent.includes('HATA_FRONT')) {
                    tr.style.display = 'none';
                } else if (tr.children[0].textContent.includes('HA_AT') &&
                    tr.style.backgroundColor === '' &&
                    tr.children[0].getAttribute('_parents') != 'null') {
                    tr.style.display = 'none';
                } else if (tr.style.backgroundColor === '') {
                    tr.style.display = 'table-row';
                } else {
                    tr.style.display = 'none';
                }
            });
        }

        function showItems() {
            document.querySelectorAll('.none')?.forEach((v) => {
                v.classList.toggle('on');
            });
        }

        function dateFormat(date) {
            if (date === null || date === undefined) {
                return '';
            }
            return new Date(date).toLocaleDateString('ja-JP') !== 'Invalid Date' ? new Date(date).toLocaleDateString('ja-JP') : '';
        }

        const CopyAsScrum = async () => {
            disp();
            const groupedByReleaseDateAndStatus = Array.from(document.querySelectorAll('#backlogissue tbody tr'))
                .filter(tr => tr.style.display !== 'none' && !Array.from(tr.classList).some(className => className.startsWith('child-')))
                .reduce((groups, tr) => {
                    const releaseDate = tr.children[6].textContent === '' ? '未定' : tr.children[6].textContent;
                    const status = tr.children[3].textContent;
                    if (!groups[releaseDate]) {
                        groups[releaseDate] = {};
                    }
                    if (!groups[releaseDate][status]) {
                        groups[releaseDate][status] = [];
                    }
                    groups[releaseDate][status].push(tr);
                    return groups;
                }, {});

            const copiedText = Object.entries(groupedByReleaseDateAndStatus)
                .sort(([release1], [release2]) => {
                    if (release1 === '未定') {
                        return 1;
                    }
                    if (release2 === '未定') {
                        return -1;
                    }
                    return new Date(release1) - new Date(release2);
                })
                .map(([release, statusGroups]) => {
                    const statusTexts = Object.entries(statusGroups).map(([status, trs]) => {
                        const issues =
                            trs.map(tr => {
                                const issueKey = tr.children[0].textContent.trim();
                                const summary = tr.children[1].textContent;
                                const link = `https://dip-dev.backlog.jp/view/${issueKey}`;
                                const remarks = '';
                                return `    - [${issueKey}](${link}) ${summary}\n      - 備考：${remarks}`;
                            }).join('\n');
                        return `  - ${statusName.filter((v)=>Object.keys(v)[0] === status)[0][status] ?? status}\n${issues}`;
                    }).join('\n');
                    return `- リリース予定日：${release}\n${statusTexts}`;
                }).join('\n');
            await navigator.clipboard.writeText(copiedText);
        }

        async function CopyAsMpMtg() {
            showParentsHasChild();
            const RFC_HAAT_ISSUES = await getRFC_HAAT_Issues();
            const HATA_FRONT_ISSUES = await getHATA_FRONT_Issues();
            const issues = RFC_HAAT_ISSUES.concat(HATA_FRONT_ISSUES);
            const filteredIssues = issues.filter(issue => issues.some(otherIssue => otherIssue.summary.includes(issue.issueKey) ? issue.group = otherIssue.id : false));

            await Promise.all(filteredIssues.map(async (v) => {
                v.children = await getHATA_FRONT_CHILD_Issues([v.group]);
                v.titleText = ` - [${v.issueKey}](https://dip-dev.backlog.jp/view/${v.issueKey}) ${v.summary}\n`;
                let cnt = 0;
                v.scheduleText = '    - スケジュール\n';
                v.scheduleText += v.children.filter((v) => v.startDate !== undefined).sort((a, b) => {
                    return new Date(a.startDate) - new Date(b.startDate);
                }).sort((a, b) => {
                    return new Date(a.dueDate) - new Date(b.dueDate);
                }).sort((a, b) => {
                    if (a.summary.includes('調査')) return -1;
                    if (b.summary.includes('調査')) return 1;
                    if (a.summary.includes('スケルトン')) return -1;
                    if (b.summary.includes('スケルトン')) return 1;
                    if (b.summary.includes('開発')) return 1;
                    if (a.summary.includes('開発')) return -1;
                    if (b.summary.includes('開発')) return 1;
                    if (a.summary.includes('単')) return -1;
                    if (b.summary.includes('単')) return 1;
                    if (a.summary.includes('受入')) return -1;
                    if (b.summary.includes('受入')) return 1;
                }).map((child) => {
                    cnt++;
                    const cstatusLabel = HATA_FRONT_ISSUES.filter(
                        (front) => front.id == child.parentIssueId)[0]?.status.name;
                    v.statusText = `    - ステータス\n      - ${statusName.filter((v)=>Object.keys(v)[0] === cstatusLabel)[0][cstatusLabel] }\n`;
                    const releaseDate = HATA_FRONT_ISSUES.filter(
                        (front) => front.id == child.parentIssueId)[0]?.customFields?.filter((hata) => hata.name == 'リリース日')[0]?.value;
                    if (!child.category.some((cat) => cat.id == '1074428308')) {
                        let last = child.summary.lastIndexOf("：");
                        child.summary = child.summary.slice(last + 1).trim();
                        if (dateFormat(child.startDate) === '') {
                            return `      - 調整中： ${child.summary}`;
                        } else {
                            return `      - ${dateFormat(child.startDate).replace(/^\d{4}\//, "")} - ${dateFormat(child.dueDate).replace(/^\d{4}\//, "")}： ${child.summary} ${child?.status.id == '3' || child?.status.id == '4' ? '【済】' : ''}`;
                        }
                    }
                    if (v.children.filter((v) => v.startDate !== undefined).length == cnt && dateFormat(releaseDate) != '') {
                        cnt = 0;
                        return `      - ${dateFormat(releaseDate).replace(/^\d{4}\//, "")}　　　  ： 本番リリース予定`;
                    }
                }).filter((s) => s !== undefined).join('\n');
            }));
            const copiedText = filteredIssues.map((v) => {
                if (v.statusText === undefined) {
                    v.statusText = '    - ステータス\n      - スケジュール作成中\n';
                    v.scheduleText = ''
                }
                return v.titleText + v.statusText + v.scheduleText;
            }).join('\n');
            await navigator.clipboard.writeText(copiedText);
        }

        const getChild = async (id) => {
            if (document.getElementById(id).dataset.children === 'true') {
                Array.from(document.getElementsByClassName('child-' + id)).map((v) =>
                    v.style.display == 'none' ? v.style.display = 'table-row' : v.style.display = 'none')
                return false;
            }
            document.getElementById(id).dataset.children = true;

            const children = await getHATA_FRONT_CHILD_Issues([id]);

            if (children.length === 0) {
                document.getElementById(id).style.display = 'none'
                return false;
            }

            [...children].map(issue => {
                const tr = document.createElement('tr');
                const link = `https://dip-dev.backlog.jp/view/${issue.issueKey}`;
                let style = document.getElementById(id)?.parentElement.parentElement.children[3]?.classList.length === 1 ? 'none' : 'on'
                tr.innerHTML = `
                <td _parents='${issue.parentIssueId ?? ''}'><a href='${link}' target='_blank'>${issue.issueKey}</a></td>
                <td>${issue.summary.replace(/【(?!SEO施策|AT取込).*?】|ADOBE-\d{4}/g, '')}</td>
                <td>${issue.assignee?.name !== undefined ? issue.assignee?.name : ''}</td>
                <td class="${style}">${issue.status.name}</td>
                <td class="${style}">${dateFormat(issue.startDate)}</td>
                <td class="${style}">${dateFormat(issue.dueDate)}</td>
                <td>${dateFormat([...issue.customFields].filter((v) => v.name === 'リリース日' && v.value !== null)[0]?.value)}</td>
            `;

                document.getElementById(id).closest('tr').after(tr);
                tr.style.backgroundColor = '#CCFFCC';
                tr.classList.add('child-' + id);
            })
        }

        const tabIssue = async (tabPanel) => {
            document.querySelector(tabPanel).innerHTML = '';
            const excludes = ['RFC-11165', 'RFC-10542', 'RFC-10485', 'HATA_FRONT-1135'];
            const RFC_HAAT_ISSUES = await getRFC_HAAT_Issues();
            const HATA_FRONT_ISSUES = await getHATA_FRONT_Issues();
            const issues = RFC_HAAT_ISSUES.concat(HATA_FRONT_ISSUES);

            [...issues].filter((v) => tabIssueFilter(v, tabPanel, excludes)).map(issue => {
                const button = document.createElement('button');
                const issueType = issue.issueKey.includes('RFC-') ? 'rfc' : 'at';
                button.className = `ticket-tag is-${issueType}`;
                const link = `https://dip-dev.backlog.jp/view/${issue.issueKey}`;

                button.innerHTML = `<a href='${link}' target='_blank'>${issue.issueKey}</a>`;

                document.querySelector(tabPanel).appendChild(button);
            });
        }

        const tabIssueFilter = (issue, tabPanel, excludes) => {
            if (tabPanel === '#tab-panel-1') {
                return (issue.issueKey.includes('RFC-') || issue.issueKey.includes('HA_AT-')) && (!issue.summary.includes('スケルトン') && !excludes.includes(issue.issueKey));
            } else if (tabPanel === '#tab-panel-2') {
                return issue.issueKey.includes('RFC-') && (!issue.summary.includes('スケルトン') && !excludes.includes(issue.issueKey));
            } else if (tabPanel === '#tab-panel-3') {
                return issue.issueKey.includes('HA_AT-') && (!issue.summary.includes('スケルトン') && !excludes.includes(issue.issueKey));
            }
        }

        (async () => {

            const excludes = ['RFC-11165', 'RFC-10542', 'RFC-10485', 'HATA_FRONT-1135'];
            const RFC_HAAT_ISSUES = await getRFC_HAAT_Issues();
            const HATA_FRONT_ISSUES = await getHATA_FRONT_Issues();
            const issues = RFC_HAAT_ISSUES.concat(HATA_FRONT_ISSUES);

            issues.forEach(issue => {
                if (issues.some(otherIssue => otherIssue.summary.includes(issue.issueKey) ? issue.group = otherIssue.id : false)) {
                    issue.highlight = true;
                }
                if (issues.some(otherIssue => issue.summary.includes(otherIssue.issueKey) ? issue.group = otherIssue.id : false)) {
                    issue.highlight = true;
                }
            });

            [...issues].filter((v) => !v.summary.includes('スケルトン') && !excludes.includes(v.issueKey)).map(issue => {
                const tr = document.createElement('tr');
                const link = `https://dip-dev.backlog.jp/view/${issue.issueKey}`;
                let button = '';
                if (!issue.issueKey.includes('RFC-') && !issue.issueKey.includes('HA_AT-')) {
                    button = `<button id="${issue.id}" onclick="getChild(${issue.id})">子</button>`;
                }

                let summary = issue.summary.replace(/【(?!SEO施策|AT取込|既バグ|改修|改善|提案|改善・提案).*?】|ADOBE-\d{4}/g, '');
                if (issue.issueKey.includes('HA_AT')) {
                    summary = summary.replace(/【AT取込】/g, '');
                }
                tr.innerHTML = `
                <td class="child" _id="${issue?.group}" _parents='${issue?.parentIssueId}'><a href='${link}' target='_blank'>${issue.issueKey}</a></td>
                <td>${summary}</td>
                <td>${issue.assignee?.name}</td>
                <td class='none'>${issue.status.name}</td>
                <td class='none'>${dateFormat(issue.startDate)}</td>
                <td class='none'>${dateFormat(issue.dueDate)}</td>
                <td>${dateFormat([...issue.customFields].filter((v) => v.name === 'リリース日' && v.value !== null)[0]?.value)}</td>
                <td>${button}</td>
            `;

                if (issue.highlight) {
                    tr.style.backgroundColor = '#BBBBFF';
                }
                document.querySelector('#backlogissue tbody').appendChild(tr);
            });


            // 課題ID一覧
            [...issues].filter((v) => (v.issueKey.includes('RFC-') || v.issueKey.includes('HA_AT-')) && (!v.summary.includes('スケルトン') && !excludes.includes(v.issueKey))).map(issue => {
                const button = document.createElement('button');
                const issueType = issue.issueKey.includes('RFC-') ? 'rfc' : 'at';
                button.className = `ticket-tag is-${issueType}`;
                const link = `https://dip-dev.backlog.jp/view/${issue.issueKey}`;

                button.innerHTML = `<a href='${link}' target='_blank'>${issue.issueKey}</a>`;

                document.querySelector('#tab-panel-1').appendChild(button);
            });


            const statusList = [...new Set(issues.map(issue => issue.status.name))];
            const statusContainer = document.createElement('div');

            statusList.forEach(status => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = status;
                checkbox.id = `status-${status}`;

                const label = document.createElement('label');
                label.htmlFor = `status-${status}`;
                label.textContent = status;

                statusContainer.appendChild(checkbox);
                statusContainer.appendChild(label);
            });

            const backlogissue = document.querySelector('#backlogissue');
            backlogissue.parentNode.insertBefore(statusContainer, backlogissue);


            statusContainer.addEventListener('change', () => {
                const selectedStatuses = Array.from(statusContainer.querySelectorAll('input:checked')).map(input => input.value);
                document.querySelectorAll('#backlogissue tbody tr')?.forEach(tr => {
                    if (selectedStatuses.length === 0 || selectedStatuses.includes(tr.children[3].textContent)) {
                        tr.style.display = 'table-row';
                    } else {
                        tr.style.display = 'none';
                    }
                });
            });

            const assigneeList = [...new Set(issues.filter((v) => v.assignee?.name != undefined).map(issue => issue.assignee?.name))];
            const assigneeSelect = document.querySelector('#assignee');
            assigneeSelect.innerHTML = `
                <option value="">すべて</option>
                ${assigneeList.map(assignee => `<option value="${assignee}">${assignee}</option>`).join('')}
            `;

            assigneeSelect.addEventListener('change', () => {
                const selectedAssignee = assigneeSelect.value;
                document.querySelectorAll('#backlogissue tbody tr')?.forEach(tr => {
                    if (selectedAssignee === '' || tr.children[2].textContent === selectedAssignee) {
                        tr.style.display = 'table-row';
                    } else {
                        tr.style.display = 'none';
                    }
                });
            });

        })();
    </script>
</body>
