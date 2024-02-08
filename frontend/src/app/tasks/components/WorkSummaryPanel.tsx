'use client'
import { useState } from 'react';
import Stack from '@mui/joy/Stack';
import AccordionGroup from '@mui/joy/AccordionGroup';
import Accordion from '@mui/joy/Accordion'
import AccordionDetails, { accordionDetailsClasses } from '@mui/joy/AccordionDetails';
import AccordionSummary, { accordionSummaryClasses } from '@mui/joy/AccordionSummary';
import Card from '@mui/joy/Card';
import CardContent from '@mui/joy/CardContent'
import Divider from '@mui/joy/Divider';
import Table from '@mui/joy/Table';
import { Tooltip } from '@mui/joy';
import Avatar from '@mui/joy/Avatar'
import List from '@mui/joy/List';
import ListItem from '@mui/joy/ListItem';
import ListItemContent from '@mui/joy/ListItemContent';
import LinearProgress from '@mui/joy/LinearProgress';
import Typography from '@mui/joy/Typography'
import { Beach24Filled, AddSubtractCircle24Filled, CalendarDataBar24Regular, Info16Regular } from '@fluentui/react-icons';
import { useGetWorkSummary } from '../hooks/useWorkSummary';
import { useGetCurrentUser } from '@/hooks/useGetCurrentUser/useGetCurrentUser';
import { getYear } from 'date-fns';

type WorkSummaryProps = {
  contentSidebarExpanded: boolean
}

const thisYear = getYear(new Date())

export const WorkSummaryPanel = ({contentSidebarExpanded} : WorkSummaryProps) => {
  const [index, setIndex] = useState<number | null>(0);
  const summary = useGetWorkSummary()
  const user = useGetCurrentUser()
  const currentCapacity = user.capacities?.find(x => x.isCurrent)

  return (
       <AccordionGroup
          variant="plain"
          transition="0.2s"
          sx={{
            maxWidth: "fit-content",
            borderRadius: 'md',
          [`& .${accordionDetailsClasses.content}.${accordionDetailsClasses.expanded}`]:
            {
              paddingBlock: '1rem',
            },
          [`& .${accordionSummaryClasses.button}`]: {
            paddingBlock: '1rem',
          },
          }}
        >
          <Accordion
             expanded={index === 0 && contentSidebarExpanded}
             onChange={(event, expanded) => {
               setIndex(expanded ? 0 : null);
             }}
          >
            <AccordionSummary>
              <Avatar color="primary">
                <CalendarDataBar24Regular />
              </Avatar>
              <ListItemContent>
                <Typography level="title-md" noWrap={!contentSidebarExpanded}>Worked hours</Typography>
                <Typography level="body-sm" noWrap={!contentSidebarExpanded}>
                  View cumulative and segmented worked hours
                </Typography>
              </ListItemContent>
            </AccordionSummary>
            <AccordionDetails>
              <List size="sm">
                <ListItem>
                  <ListItemContent sx={{fontWeight: 500}}>Today:</ListItemContent>
                  <Typography level="body-sm">{summary?.todayText}</Typography>
                </ListItem>
                <ListItem>
                  <ListItemContent sx={{fontWeight: 500}}>This week:</ListItemContent>
                  <Typography level="body-sm">{summary?.weekText}</Typography>
                </ListItem>
              </List>
              <Divider sx={{my: 1}} orientation='horizontal'/>
              <Typography level="title-sm" sx={{my:1}}>Hours by project</Typography>
              <Table aria-label="hours by project for today and current week" sx={{mt: 1, '& .vacation':{bgcolor: 'primary.softBg'} ,'& thead th:nth-of-type(1)': { width: '45%' }}}>
                <thead>
                  <tr>
                    <th>Project</th>
                    <th>Today</th>
                    <th>Week</th>
                  </tr>
                </thead>
                <tbody>
                  {summary.projectSummaries?.map((projSummary) => (
                    <tr key={projSummary.projectId} className={projSummary.isVacation ? "vacation": ""}>
                      <td>{projSummary.project}</td>
                      <td>{projSummary.todayText}</td>
                      <td>{projSummary.weekText}</td>
                    </tr>
                  ))}
                </tbody>
                <tfoot>
                  <tr>
                    <th scope="row">Total</th>
                    <th>{summary.todayText}</th>
                    <th>{summary.weekText}</th>
                  </tr>
                </tfoot>
              </Table>
            </AccordionDetails>
          </Accordion>
          <Accordion
          expanded={index === 1 && contentSidebarExpanded}
          onChange={(event, expanded) => {
            setIndex(expanded ? 1 : null);
          }}
          >
            <AccordionSummary>
              <Avatar color="danger">
                <AddSubtractCircle24Filled />
              </Avatar>
              <ListItemContent>
                <Typography level="title-md" noWrap={!contentSidebarExpanded}>Over/under hours</Typography>
                <Typography level="body-sm" noWrap={!contentSidebarExpanded}>
                  Amount of hours ahead or behind expected
                </Typography>
              </ListItemContent>
            </AccordionSummary>
            <AccordionDetails>
              <Typography level="body-xs" sx={{p: 1.5, mx: 0.5, mb: 0.5}} variant="soft" color="primary">
                Based on your capacity ({currentCapacity?.capacity } hours per day), you will have an expected number of hours to be worked for the week and year. The worked numbers below are calculated as of today.
              </Typography>
              <Stack direction="column" spacing={1} sx={{mt: 1}} justifyContent="space-between">
                  <Divider>Week</Divider>
                  <List size="sm">
                  <ListItem>
                    <ListItemContent sx={{fontWeight: 500}}>Worked:</ListItemContent>
                    <Typography level="body-sm">{(summary?.week ?? 0) / 60} h</Typography>
                  </ListItem>
                  <ListItem>
                    <ListItemContent sx={{fontWeight: 500}}>Expected:</ListItemContent>
                    <Typography level="body-sm">{summary.expectedHoursWeek} h</Typography>
                  </ListItem>
                  <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Ahead/behind expected:</ListItemContent>
                      <Typography level="body-sm">{((summary.week?? 0) /60) - (summary.expectedHoursWeek ?? 0)} h</Typography>
                    </ListItem>
                </List>
                  <Divider>Year</Divider>
                   <List size="sm">
                    <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Worked:</ListItemContent>
                      <Typography level="body-sm">{summary.workedHoursYear} h</Typography>
                    </ListItem>
                    <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Total expected so far:</ListItemContent>
                      <Typography level="body-sm">{summary.expectedHoursToDate} h</Typography>
                    </ListItem>
                    <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Ahead/behind pace for year:</ListItemContent>
                      <Typography level="body-sm">{(summary.workedHoursYear ?? 0) - (summary.expectedHoursToDate ?? 0)} h</Typography>
                    </ListItem>
                    <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Total expected for year:</ListItemContent>
                      <Typography level="body-sm">{summary.expectedHoursYear} h</Typography>
                    </ListItem>
                    <ListItem>
                      <ListItemContent sx={{fontWeight: 500}}>Ahead/behind total for year:</ListItemContent>
                      <Typography level="body-sm">{(summary.workedHoursYear ?? 0) - (summary.expectedHoursYear ?? 0)} h</Typography>
                    </ListItem>
                  </List>
              </Stack>
            </AccordionDetails>
          </Accordion>
          <Accordion
            expanded={index === 2 && contentSidebarExpanded}
            onChange={(event, expanded) => {
              setIndex(expanded ? 2 : null);
            }}
          >
            <AccordionSummary>
                <Avatar color="success">
                  <Beach24Filled/>
                </Avatar>
              <ListItemContent>
                <Typography level="title-md" noWrap={!contentSidebarExpanded}>Vacation</Typography>
                <Typography level="body-sm" noWrap={!contentSidebarExpanded}>View scheduled and available vacation</Typography>
              </ListItemContent>
            </AccordionSummary>
            <AccordionDetails>
              <Card variant="soft" color="primary" invertedColors>
                  <CardContent>
                    <Typography level="title-lg">Available for {thisYear}</Typography>
                    <Typography level="title-md">{summary.vacationAvailableText}</Typography>
                  </CardContent>
              </Card>
              <Divider sx={{my: 1}} orientation='horizontal'>Status</Divider>
              <Stack direction="column" spacing={1} sx={{mx: 0.5}}>
                <Stack direction="row" spacing={1} sx={{my: 0.75}} justifyContent="space-between">
                  <Typography level="title-sm">
                    <Tooltip title="Vacation hours already used this year" variant="soft">
                      <Info16Regular/>
                    </Tooltip> Used:
                  </Typography>
                  <Typography level="body-sm">{summary.vacationUsedText}</Typography>
                </Stack>
                <LinearProgress variant="outlined" size="lg" color="primary" value={((summary.vacationUsed ?? 0) / (summary.vacationAvailable ?? 0)) * 100} determinate sx={{ my: 1 }} />
                <Stack direction="row" spacing={1} sx={{my: 0.75}} justifyContent="space-between">
                  <Typography level="title-sm">
                    <Tooltip title="Vacation hours added to calendar and not yet used this year" variant="soft">
                      <Info16Regular/>
                    </Tooltip> Scheduled:
                  </Typography>
                  <Typography level="body-sm">{summary.vacationScheduledText}</Typography>
                </Stack>
                <LinearProgress variant="outlined" size="lg" color="neutral" value={((summary.vacationScheduled ?? 0) / (summary.vacationAvailable ?? 0)) * 100} determinate sx={{ my: 1 }} />
                <Stack direction="row" spacing={1} sx={{my: 0.75}} justifyContent="space-between">
                  <Typography level="title-sm">
                    <Tooltip title="Vacation hours not used or scheduled" variant="soft">
                      <Info16Regular/>
                    </Tooltip> Pending:
                  </Typography>
                  <Typography level="body-sm">{summary.vacationPendingText}</Typography>
                </Stack>
                <LinearProgress variant="outlined" size="lg" color="neutral" value={((summary.vacationPending ?? 0) / (summary.vacationAvailable ?? 0)) * 100} determinate sx={{ my: 1 }} />
              </Stack>
            </AccordionDetails>
          </Accordion>
        </AccordionGroup>

  )
}
